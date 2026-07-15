@extends('frontend.layouts.app')
@section('title', __('messages.Order Hotel'))

@push('styles')
    <link rel="stylesheet" type="text/css" href="{{ asset('panel/styles/icon-font.min.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="{{ mix('build/frontend/css/pages/hotel-booking-entry.css') }}">
@endpush

@push('scripts')
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="{{ asset('panel/script/core.js') }}"></script>
    <script src="{{ mix('build/frontend/js/pages/hotel-booking.js') }}" defer></script>
@endpush

@section('content')
    <div class="frontend-page-shell hotel-booking-page hotel-booking-page--normal frontend-availability-family-page">
        @include('partials.hotel-booking-topband', [
            'currentPageLabel' => __('messages.Standard Rate Booking'),
            'bookingBadge' => __('messages.Normal Price'),
            'bookingDescription' => __('messages.Confirm the contract rate selection and complete guest, transfer, and booking information before sending the hotel request.'),
            'selectedOfferLabel' => count($promotions) > 0 ? $promotions_name : __('messages.Contract Rate'),
            'selectedOfferNote' => __('messages.Contract pricing and negotiated booking terms are applied to this booking flow for the selected stay dates.'),
            'useAvailabilityFamily' => true,
        ])

        <div class="container frontend-content-section">
            @include('partials.alerts')
            <div class="row g-4 hotel-booking-layout">
                <div class="col-12">
                    <div class="card-box frontend-availability-family-surface">
                        <form
                            id="create-order"
                            action="{{ route('func.create.order-hotel-normal') }}"
                            method="POST"
                            data-booking-form
                            data-review-hotel="{{ $hotel->name }}"
                            data-review-room="{{ $room->rooms }}"
                            data-review-checkin="{{ dateFormat($checkin) }}"
                            data-review-checkout="{{ dateFormat($checkout) }}"
                            data-review-duration="{{ $duration }} {{ $duration > 1 ? __('messages.nights') : __('messages.Night') }}"
                            data-booking-variant="standard"
                            data-room-max="8"
                            data-quote-room-max="30"
                            data-extra-bed-mode="stay"
                            data-currency-digits="0"
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
                            @canany(['posDev','posAuthor','posRsv'])
                                <div class="row">
                                    @include('partials.admin-create-order', compact('agents'))
                                </div>
                            @endcanany

                            <div class="booking-wizard" data-booking-wizard>
                                @include('partials.hotel-booking-wizard-nav', [
                                    'wizardSteps' => [
                                        ['title' => __('messages.Stay and guests'), 'description' => __('messages.Standard selection')],
                                        ['title' => __('messages.Airport Shuttle'), 'description' => __('messages.Optional arrival support')],
                                        ['title' => __('messages.Review and submit'), 'description' => __('messages.Standard total')],
                                    ],
                                ])

                                <section class="booking-wizard__panel is-active" data-wizard-panel="1">
                                    <div class="booking-wizard__heading">
                                        <div>
                                            <div class="booking-wizard__eyebrow">@lang('messages.Step 1')</div>
                                            <h2 class="booking-wizard__title">@lang('messages.Standard stay and guest details')</h2>
                                        </div>
                                        <p class="booking-wizard__text">@lang('messages.Review the selected contract offer and fill in the guest information for the requested rooms.')</p>
                                    </div>

                                    <section class="frontend-detail-block">
                                        <div class="frontend-fact-grid">
                                            <div class="frontend-fact-card frontend-fact-card--wide">
                                                <span>@lang('messages.Offer')</span>
                                                <strong>{{ count($promotions) > 0 ? $promotions_name : __('messages.Contract Rate') }}</strong>
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
                                            @if (!empty(trim(strip_tags((string) $room->localized_include))))
                                                <div class="frontend-fact-card frontend-fact-card--fullrow">
                                                    <span>@lang('messages.Include')</span>
                                                    <div class="frontend-fact-card__content">{!! $room->localized_include !!}</div>
                                                </div>
                                            @endif
                                        </div>
                                    </section>

                                    <section>
                                        <div class="frontend-detail-block__header p-t-18">
                                            <div>
                                                <h3 class="frontend-detail-block__title">@lang('messages.Guest and room details')</h3>
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
                                                <button id="add" type="button" class="btn btn-primary hotel-booking-page__add-room"><i class="icon-copy fa fa-plus-circle" aria-hidden="true"></i> @lang('messages.Add More Room')</button>
                                            </div>
                                        </div>
                                    </section>

                                    <div class="booking-wizard__actions">
                                        <button type="button" class="btn btn-primary" data-wizard-next>@lang('messages.Continue to transfers')</button>
                                    </div>
                                </section>

                                <section class="booking-wizard__panel" data-wizard-panel="2">
                                    <div class="booking-wizard__heading">
                                        <div>
                                            <div class="booking-wizard__eyebrow">@lang('messages.Step 2')</div>
                                            <h2 class="booking-wizard__title">@lang('messages.Airport shuttle')</h2>
                                        </div>
                                        <p class="booking-wizard__text">@lang('messages.Only add arrival and departure transfer details if needed for this booking request.')</p>
                                    </div>

                                    @include('partials.hotel-booking-transfer-fields')

                                    <div class="booking-wizard__actions">
                                        <button type="button" class="btn btn-danger" data-wizard-prev>@lang('messages.Back')</button>
                                        <button type="button" class="btn btn-primary" data-wizard-next>@lang('messages.Continue to review')</button>
                                    </div>
                                </section>

                                <section class="booking-wizard__panel" data-wizard-panel="3">
                                    <div class="booking-wizard__heading">
                                        <div>
                                            <div class="booking-wizard__eyebrow">@lang('messages.Step 3')</div>
                                            <h2 class="booking-wizard__title">@lang('messages.Review booking and submit')</h2>
                                        </div>
                                        <p class="booking-wizard__text">@lang('messages.Review the booking total, hotel references, and hidden booking metadata before submitting the request.')</p>
                                    </div>

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
                                                <h3 class="frontend-detail-block__title">@lang('messages.Standard total breakdown')</h3>
                                            </div>
                                            <div class="frontend-detail-block__meta">@lang('messages.Final step')</div>
                                        </div>
                                        <div class="box-price-kicked">
                                            <div class="row">
                                                <div class="col-6 col-md-6">
                                                    <div id="suitesAndVillasText" class="normal-text">@lang('messages.Suites and Villas')</div>
                                                    <div id="extraBedText" class="normal-text">@lang('messages.Extra Bed')</div>
                                                    <div id="airportShuttle" class="normal-text">@lang('messages.Airport Shuttle')</div>
                                                    <div id="promotionsText" class="normal-text">@lang('messages.Promotions')</div>
                                                    <div id="kickBackText" class="normal-text">@lang('messages.Kick Back')</div>
                                                    <hr class="form-hr">
                                                    <div class="total-price">@lang('messages.Total Price')</div>
                                                </div>
                                                <div class="col-6 col-md-6 text-right">
                                                    <div id="suitesAndVillasPrice" class="text-price"><span id="suitesAndVillasPriceLable"></span></div>
                                                    <div id="extraBedPrice" class="text-price"><span id='extraBedPriceTotal'></span></div>
                                                    <div id="airportShuttlePrice" class="text-price"><span id="airportShuttleText"></span></div>
                                                    <div id="promotionsDiscount" class="promo-text"><span id='promotionsDiscountTotal'></span></div>
                                                    <div id="kickBackAmount" class="promo-text"><span id='kickBackDiscount'></span></div>
                                                    <hr class="form-hr">
                                                    <div class="total-price"><span id="finalprice">{{ currencyFormatUsd($final_price) }}</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <input type="hidden" name="orderno" value="{{ $orderNumber }}">
                                    <input type="hidden" name="service" value="Hotel Normal">
                                    <input type="hidden" name="subservice" value="{{ $room->rooms }}">
                                    <input type="hidden" name="subservice_id" value="{{ $room->id }}">
                                    <input type="hidden" name="hotel_id" value="{{ $hotel->id }}">
                                    <input type="hidden" name="room_id" value="{{ $room->id }}">
                                    <input type="hidden" name="duration" id="duration" value="{{ $duration }}">
                                    <input type="hidden" name="airport_shuttle_in_price_id" id="airport_shuttle_in_price_id" value="">
                                    <input type="hidden" name="airport_shuttle_out_price_id" id="airport_shuttle_out_price_id" value="">
                                    <input type="hidden" name="service_id" value="{{ $hotel->id }}">
                                    <input type="hidden" name="servicename" value="{{ $hotel->name }}">
                                    <input type="hidden" name="page" value="order-normal-hotel">
                                    <input type="hidden" name="action" value="Create Order">
                                    <input type="hidden" name="location" value="{{ $hotel->region }}">
                                    <input type="hidden" name="final_price" id="final_price" value="{{ $final_price }}">
                                    <input type="hidden" name="var_kick_back_per_pax" id='var_kick_back_per_pax' value="{{ $kick_back_per_pax }}">
                                    <input type="hidden" name="var_kick_back_per_room" id='var_kick_back_per_room' value="{{ $kick_back }}">
                                    <input type="hidden" name="var_kick_back_total" id='var_kick_back_total' value="{{ $kick_back }}">
                                    <input type="hidden" name="var_normal_price" id='var_normal_price' value="{{ $normal_price }}">
                                    <input type="hidden" name="promotions_id" value="{{ $promotions_id }}">
                                    <input type="hidden" name="promotions_discounts" value="{{ $promotions_discount }}">
                                    <input type="hidden" name="promotions" value="{{ $promotions_name }}">
                                    <input type="hidden" name="var_promotions_discount" id='var_promotions_discount' value="{{ $total_promotions_discount }}">
                                    @include('partials.order-confirmation-checkbox', [
                                        'id' => 'hotelNormalTermsAccepted',
                                    ])
                                    <div class="booking-wizard__actions">
                                        <button type="button" class="btn btn-danger" data-wizard-prev>@lang('messages.Back')</button>
                                        <button
                                            type="submit"
                                            form="create-order"
                                            id="normal-reserve"
                                            class="btn btn-primary"
                                            data-processing-label="@lang('messages.Processing')..."
                                        ><i class="icon-copy fa fa-shopping-basket" aria-hidden="true"></i> @lang('messages.Order')</button>
                                        <button type="button" onclick="goBack()" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
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
