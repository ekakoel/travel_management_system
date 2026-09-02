@extends('frontend.layouts.app')
@section('title', __('messages.Order Hotel'))

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('panel/styles/icon-font.min.css') }}">
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/hotel-booking-entry.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/frontend/js/pages/hotel-booking.js') }}" defer></script>
@endpush

@section('content')
    <div class="frontend-page-shell hotel-booking-page hotel-booking-page--promo frontend-availability-family-page">
        @include('partials.hotel-booking-topband', [
            'currentPageLabel' => __('messages.Promotion Booking'),
            'bookingBadge' => __('messages.Promotion Price'),
            'bookingDescription' => __('messages.Review the selected promotion, complete traveller details, and confirm optional services before sending the hotel booking request.'),
            'selectedOfferLabel' => $promo_name,
            'selectedOfferNote' => __('messages.Promotion pricing and included benefits are applied to this booking flow for the selected stay dates.'),
            'useAvailabilityFamily' => true,
        ])

        <div class="container frontend-content-section">
            @include('partials.alerts')
            <div class="row g-4 hotel-booking-layout">
                <div class="col-12">
                    <div class="card-box frontend-availability-family-surface">
                        <form
                            id="create-order"
                            action="{{ route('func.create.order-hotel-promo') }}"
                            method="POST"
                            data-booking-form
                            data-review-hotel="{{ $hotel->name }}"
                            data-review-room="{{ $room->rooms }}"
                            data-review-checkin="{{ dateFormat($checkin) }}"
                            data-review-checkout="{{ dateFormat($checkout) }}"
                            data-stay-checkin="{{ dateFormat($checkin) }}"
                            data-stay-checkout="{{ dateFormat($checkout) }}"
                            data-review-duration="{{ $duration }} {{ $duration > 1 ? __('messages.nights') : __('messages.Night') }}"
                            data-booking-variant="promo"
                            data-room-max="8"
                            data-quote-room-max="30"
                            data-extra-bed-mode="nightly"
                            data-currency-digits="2"
                            data-label-to-be-advised="@lang('messages.To be advised')"
                            data-label-room="@lang('messages.Room')"
                            data-label-lead-room="@lang('messages.Lead room')"
                            data-label-additional-room="@lang('messages.Additional room')"
                            data-label-select-transport="@lang('messages.Select Transport')"
                            data-label-not-added="@lang('messages.Not added')"
                            data-label-flight-prefix="@lang('messages.Flight')"
                            data-label-guest-singular="@lang('messages.Guest')"
                            data-label-guest-plural="@lang('messages.Guests')"
                            data-label-extra-bed="@lang('messages.Extra Bed')"
                            data-label-none="@lang('messages.None')"
                            data-label-no-extra-bed="@lang('messages.No extra bed available')"
                            data-label-special-day="@lang('messages.Special Day')"
                            data-label-guest-details-pending="@lang('messages.Guest details pending')"
                            data-label-guest-names-missing="@lang('messages.Guest names not filled yet')"
                            data-label-review-empty="@lang('messages.Add guest names and rooming details to review them here.')"
                            data-label-no-remark="@lang('messages.No remark added.')"
                            data-label-quote-request="@lang('messages.Quote request')"
                            data-label-quote-review="@lang('messages.This order will be handled as a quote request because it contains more than 8 rooms.')"
                        >
                            @csrf
                            <input type="hidden" name="hotel_booking_version" value="2">
                            @include('partials.form-submission-token')
                            @canany(['posDev','posAuthor','posRsv'])
                                <div class="row">
                                    @include('partials.admin-create-order', compact('agents'))
                                </div>
                            @endcanany

                            <div class="booking-wizard" data-booking-wizard>
                                @include('partials.hotel-booking-wizard-nav', [
                                    'wizardSteps' => [
                                        ['title' => __('messages.Stay and rooms'), 'description' => __('messages.Room occupancy')],
                                        ['title' => __('messages.Guests and transfers'), 'description' => __('messages.Guest manifest and optional transfer')],
                                        ['title' => __('messages.Review and submit'), 'description' => __('messages.Promotion total')],
                                    ],
                                ])

                                <section class="booking-wizard__panel is-active" data-wizard-panel="1">
                                    <div class="booking-wizard__heading">
                                        <div>
                                            <div class="booking-wizard__eyebrow">@lang('messages.Step 1')</div>
                                            <h2 class="booking-wizard__title">@lang('messages.Stay and room details')</h2>
                                        </div>
                                        <p class="booking-wizard__text">@lang('messages.Set the adults, children, child ages, and room requirements for every room.')</p>
                                    </div>

                                    <section class="frontend-detail-block">
                                        
                                        <div class="frontend-fact-grid">
                                            <div class="frontend-fact-card frontend-fact-card--wide">
                                                <span>@lang('messages.Promotion')</span>
                                                <strong>{{ $promo_name }}</strong>
                                            </div>
                                            <div class="frontend-fact-card">
                                                <span>@lang('messages.Room')</span>
                                                <strong>{{ $room->rooms }}</strong>
                                            </div>
                                            <div class="frontend-fact-card">
                                                <span>@lang('messages.Stay')</span>
                                                <strong>{{ $duration }} {{ $duration > 1 ? __('messages.nights') : __('messages.Night') }}</strong>
                                            </div>
                                            <div class="frontend-fact-card frontend-fact-card--wide">
                                                <span>@lang('messages.Dates')</span>
                                                <strong>{{ dateFormat($checkin) }} - {{ dateFormat($checkout) }}</strong>
                                            </div>
                                            @if (!empty(trim(strip_tags((string) $promo_include))))
                                                <div class="frontend-fact-card frontend-fact-card--fullrow">
                                                    <span>@lang('messages.Include')</span>
                                                    <div class="frontend-fact-card__content">{!! $promo_include !!}</div>
                                                </div>
                                            @endif
                                            @if (!empty(trim(strip_tags((string) $promo_benefits))))
                                                <div class="frontend-fact-card frontend-fact-card--fullrow">
                                                    <span>@lang('messages.Benefits')</span>
                                                    <div class="frontend-fact-card__content">{!! $promo_benefits !!}</div>
                                                </div>
                                            @endif
                                            @if (!empty(trim(strip_tags((string) $promo_additional_info))))
                                                <div class="frontend-fact-card frontend-fact-card--fullrow">
                                                    <span>@lang('messages.Additional Information')</span>
                                                    <div class="frontend-fact-card__content">{!! $promo_additional_info !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </section>

                                    <section>
                                        <div class="frontend-detail-block__header p-t-18">
                                            <div>
                                                <h3 class="frontend-detail-block__title">@lang('messages.Room occupancy details')</h3>
                                            </div>
                                            <div class="frontend-detail-block__meta">@lang('messages.Up to 8 rooms')</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-12 room-list m-b-18">
                                                <div id="dynamic_field" class="room-list-stack">
                                                    @include('partials.hotel-booking-room-card', ['roomForm' => $roomForm, 'roomNumber' => 1, 'showRemove' => false])
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <div class="checkbox-left hotel-booking-quote-card" data-quote-card>
                                                    <input name="request_quotation" type="checkbox" class="checkbox-left__control" value="Yes" data-quote-checkbox>
                                                    <div>
                                                        <p>@lang('messages.Ask for quote rates for rooms more than 8 units')</p>
                                                        <small>@lang('messages.Enable this when the booking needs more than 8 rooms. The order will be highlighted as a quote request.')</small>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-4 text-right">
                                                <button id="add" type="button" class="ui-btn ui-btn--primary hotel-booking-page__add-room"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Add More Room')</button>
                                            </div>
                                        </div>
                                    </section>

                                    @if ($totalPriceOptionalRates > 0)
                                        <section class="frontend-detail-block">
                                            <div class="frontend-detail-block__header">
                                                <div>
                                                    <div class="frontend-detail-block__eyebrow">@lang('messages.Optional Services')</div>
                                                    <h3 class="frontend-detail-block__title">@lang('messages.Additional charge preview')</h3>
                                                </div>
                                                <div class="frontend-detail-block__meta">{{ $optional_rates->count() }} @lang('messages.options')</div>
                                            </div>
                                            <div class="frontend-table-shell">
                                                <table class="data-table table nowrap">
                                                    <thead>
                                                        <tr>
                                                            <th>@lang('messages.Date')</th>
                                                            <th>@lang('messages.Number of Guests')</th>
                                                            <th>@lang('messages.Services')</th>
                                                            <th>@lang('messages.Price')</th>
                                                            <th>@lang('messages.Total Price')</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach ($optional_rates as $optional_rate)
                                                            <tr class="optional-rate-row">
                                                                <td><div class="table-service-name">{{ dateFormat($optional_rate->active_date) }}</div></td>
                                                                <td><div class="number_of_guests table-service-name guest-total-display">0</div></td>
                                                                <td><div class="table-service-name">{{ $optional_rate->name }}</div></td>
                                                                <td><div data-price-pax="{{ $optional_rate->calculatePrice($usdrates, $tax) }}" class="price-per-pax table-service-name">{{ currencyFormatUsd($optional_rate->calculatePrice($usdrates, $tax)) }}/@lang('messages.pax')</div></td>
                                                                <td><div class="total-price-optional-rate table-service-name">{{ currencyFormatUsd(0) }}</div></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <div class="box-price-kicked m-b-8 mt-3">
                                                <div class="row">
                                                    <div class="col-6 col-md-6">
                                                        <div class="subtotal-text">@lang('messages.Additional Charges')</div>
                                                    </div>
                                                    <div class="col-6 col-md-6 text-right">
                                                        <div id="totalAdditionalCharge" class="total-all-optional-rate subtotal-price">{{ currencyFormatUsd($totalPriceOptionalRates) }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </section>
                                    @endif

                                    <div class="booking-wizard__actions">
                                        <button type="button" class="ui-btn ui-btn--primary" data-wizard-next>@lang('messages.Continue to guest details')</button>
                                    </div>
                                </section>

                                <section class="booking-wizard__panel" data-wizard-panel="2">
                                    <div class="booking-wizard__heading">
                                        <div>
                                            <div class="booking-wizard__eyebrow">@lang('messages.Step 2')</div>
                                            <h2 class="booking-wizard__title">@lang('messages.Guests and transfers')</h2>
                                        </div>
                                        <p class="booking-wizard__text">@lang('messages.Add flight timing and airport shuttle requests only when this booking needs transfer coordination.')</p>
                                    </div>

                                    @include('partials.hotel-booking-guest-manifest')
                                    @include('partials.hotel-booking-transfer-fields')

                                    <div class="booking-wizard__actions">
                                        <button type="button" class="ui-btn ui-btn--secondary" data-wizard-prev>@lang('messages.Back')</button>
                                        <button type="button" class="ui-btn ui-btn--primary" data-wizard-next>@lang('messages.Continue to review')</button>
                                    </div>
                                </section>

                                <section class="booking-wizard__panel" data-wizard-panel="3">
                                    <div class="booking-wizard__heading">
                                        <div>
                                            <div class="booking-wizard__eyebrow">@lang('messages.Step 3')</div>
                                            <h2 class="booking-wizard__title">@lang('messages.Review booking and submit')</h2>
                                        </div>
                                        <p class="booking-wizard__text">@lang('messages.Double-check the promotion, extras, and total amount before submitting the booking request.')</p>
                                    </div>
                                    @if (!empty(trim(strip_tags((string) $promo_cancellation_policy))))
                                        <div class="frontend-fact-card frontend-fact-card--fullrow">
                                            <span>@lang('messages.Cancellation Policy')</span>
                                            <div class="frontend-fact-card__content">{!! $promo_cancellation_policy !!}</div>
                                        </div>
                                    @endif
                                    <section class="frontend-detail-block">
                                        <div class="frontend-detail-block__header">
                                            <div>
                                                <div class="frontend-detail-block__eyebrow">@lang('messages.Review')</div>
                                                <h3 class="frontend-detail-block__title">@lang('messages.Stay, guests, and transfer summary')</h3>
                                            </div>
                                        </div>
                                        @include('partials.hotel-booking-review-summary')
                                    </section>

                                    <section class="frontend-detail-block">
                                        <div class="frontend-detail-block__header">
                                            <div>
                                                <div class="frontend-detail-block__eyebrow">@lang('messages.Note')</div>
                                                <h3 class="frontend-detail-block__title">@lang('messages.Remark for reservation team')</h3>
                                            </div>
                                        </div>
                                        <div class="form-group mb-0">
                                            <textarea id="note" name="note" placeholder="@lang('messages.Optional')" class="tiny_mce form-control border-radius-0" value="{{ old('note') }}"></textarea>
                                            @error('note')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </section>

                                    <section class="frontend-detail-block">
                                        <div class="frontend-detail-block__header">
                                            <div>
                                                <div class="frontend-detail-block__eyebrow">@lang('messages.Price')</div>
                                                <h3 class="frontend-detail-block__title">@lang('messages.Promotion total breakdown')</h3>
                                            </div>
                                            <div class="frontend-detail-block__meta">@lang('messages.Final step')</div>
                                        </div>
                                        <div class="box-price-kicked">
                                            <div class="row">
                                                <div class="col-6 col-md-6">
                                                    <div id="suitesAndVillasText" class="normal-text">@lang('messages.Suites and Villas')</div>
                                                    <div id="airportShuttle" class="normal-text">@lang('messages.Airport Shuttle')</div>
                                                    <div id="extraBedText" class="normal-text">@lang('messages.Extra Bed')</div>
                                                    <div id="totalAdditionalCargeText" class="normal-text">@lang('messages.Additional Charge')</div>
                                                    <hr class="form-hr">
                                                    <div class="total-price">@lang('messages.Total Price')</div>
                                                </div>
                                                <div class="col-6 col-md-6 text-right">
                                                    <div id="suitesAndVillasPrice" class="text-price"><span id="suitesAndVillasPriceLable"></span></div>
                                                    <div id="airportShuttlePrice" class="text-price"><span id="airportShuttleText"></span></div>
                                                    <div id="extraBedPrice" class="text-price"><span id='extraBedPriceTotal'>{{ currencyFormatUsd($promo_price) }}</span></div>
                                                    <div id="totalAdditionalCarge" class="text-price"><span id='totalAdditionalCargePrice'>{{ currencyFormatUsd(0) }}</span></div>
                                                    <hr class="form-hr">
                                                    <div class="total-price"><span id="finalprice">{{ currencyFormatUsd($final_price) }}</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <input type="hidden" name="orderno" value="{{ $orderNumber }}">
                                    <input type="hidden" name="service" value="Hotel Promo">
                                    <input type="hidden" name="subservice" value="{{ $room->rooms }}">
                                    <input type="hidden" name="subservice_id" value="{{ $room->id }}">
                                    <input type="hidden" name="promo_id" value="{{ $prIds }}">
                                    <input type="hidden" name="promo_name" value="{{ $promo_name }}">
                                    <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                                    <input type="hidden" name="price_list" value="{{ $price_list }}">
                                    <input type="hidden" name="airport_shuttle_in_price_id" id="airport_shuttle_in_price_id" value="">
                                    <input type="hidden" name="airport_shuttle_out_price_id" id="airport_shuttle_out_price_id" value="">
                                    <input type="hidden" name="service_id" value="{{ $hotel->id }}">
                                    <input type="hidden" name="servicename" value="{{ $hotel->name }}">
                                    <input type="hidden" name="promo_price" value="{{ $promo_price }}">
                                    <input type="hidden" name="page" value="order-promo-hotel">
                                    <input type="hidden" name="action" value="Create Order">
                                    <input type="hidden" name="location" value="{{ $hotel->region }}">
                                    <input type="hidden" name="duration" id="duration" value="{{ $duration }}">
                                    <input type="hidden" name="final_price" id="final_price" value="{{ $final_price }}">
                                    <input type="hidden" name="var_promo_price" id='var_promo_price' value="{{ $promo_price }}">
                                    @include('partials.order-confirmation-checkbox', [
                                        'id' => 'hotelPromoTermsAccepted',
                                    ])
                                    <div class="booking-wizard__actions">
                                        <button type="button" class="ui-btn ui-btn--secondary" data-wizard-prev>@lang('messages.Back')</button>
                                        <button
                                            type="submit"
                                            form="create-order"
                                            id="normal-reserve"
                                            class="ui-btn ui-btn--primary"
                                            data-processing-label="@lang('messages.Processing')..."
                                        ><i class="icon-copy fa fa-shopping-basket" aria-hidden="true"></i> @lang('messages.Order')</button>
                                        <button type="button" onclick="goBack()" class="ui-btn ui-btn--danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                    </div>
                                </section>
                            </div>
                            @include('partials.form-submit-overlay')
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
