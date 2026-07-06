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
    <div class="frontend-page-shell hotel-booking-page hotel-booking-page--package frontend-availability-family-page">
        @include('partials.hotel-booking-topband', [
            'currentPageLabel' => __('messages.Package Booking'),
            'bookingBadge' => __('messages.Package Price'),
            'bookingDescription' => __('messages.Continue with the selected accommodation package and complete the booking request with traveller details, transfers, and remarks.'),
            'selectedOfferLabel' => $package->localized_name,
            'selectedOfferNote' => __('messages.Package pricing, bundled inclusions, and booking terms are applied to this booking flow for the selected stay dates.'),
            'useAvailabilityFamily' => true,
        ])

        <div class="container frontend-content-section">
            @include('partials.alerts')
            <div class="row g-4 hotel-booking-layout">
                <div class="col-12">
                    <div class="card-box frontend-availability-family-surface">
                        <form
                            id="create-order"
                            action="{{ route('func.create.order-hotel-package',$package->id) }}"
                            method="POST"
                            data-booking-form
                            data-review-hotel="{{ $hotel->name }}"
                            data-review-room="{{ $room->rooms }}"
                            data-review-checkin="{{ dateFormat($checkin) }}"
                            data-review-checkout="{{ dateFormat($checkout) }}"
                            data-review-duration="{{ $duration }} {{ $duration > 1 ? __('messages.nights') : __('messages.Night') }}"
                            data-booking-variant="package"
                            data-room-max="8"
                            data-quote-room-max="30"
                            data-extra-bed-mode="nightly"
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
                                        ['title' => __('messages.Stay and guests'), 'description' => __('messages.Package details')],
                                        ['title' => __('messages.Airport Shuttle'), 'description' => __('messages.Arrival support')],
                                        ['title' => __('messages.Review and submit'), 'description' => __('messages.Package total')],
                                    ],
                                ])

                                <section class="booking-wizard__panel is-active" data-wizard-panel="1">
                                    <div class="booking-wizard__heading">
                                        <div>
                                            <div class="booking-wizard__eyebrow">@lang('messages.Step 1')</div>
                                            <h2 class="booking-wizard__title">@lang('messages.Package stay and guest details')</h2>
                                        </div>
                                        <p class="booking-wizard__text">@lang('messages.Review the selected package and add the guest details for the requested rooms.')</p>
                                    </div>

                                    <section class="frontend-detail-block">
                                        <div class="frontend-fact-grid">
                                            <div class="frontend-fact-card frontend-fact-card--wide">
                                                <span>@lang('messages.Package')</span>
                                                <strong>{{ $package->localized_name }}</strong>
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
                                            @if (!empty(trim(strip_tags((string) $package->localized_include))))
                                                <div class="frontend-fact-card frontend-fact-card--fullrow">
                                                    <span>@lang('messages.Include')</span>
                                                    <div class="frontend-fact-card__content">{!! $package->localized_include !!}</div>
                                                </div>
                                            @endif
                                            @if (!empty(trim(strip_tags((string) $package->localized_benefits))))
                                                <div class="frontend-fact-card frontend-fact-card--fullrow">
                                                    <span>@lang('messages.Benefits')</span>
                                                    <div class="frontend-fact-card__content">{!! $package->localized_benefits !!}</div>
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
                                        <p class="booking-wizard__text">@lang('messages.Add transfer information only if the traveller needs airport coordination.')</p>
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
                                        <p class="booking-wizard__text">@lang('messages.Review the package amount, transfer selections, and booking references before sending the request.')</p>
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
                                                <h3 class="frontend-detail-block__title">@lang('messages.Package total breakdown')</h3>
                                            </div>
                                            <div class="frontend-detail-block__meta">@lang('messages.Final step')</div>
                                        </div>
                                        <div class="box-price-kicked">
                                            <div class="row">
                                                <div class="col-6 col-md-6">
                                                    <div id="suitesAndVillasText" class="normal-text">@lang('messages.Suites and Villas')</div>
                                                    <div id="airportShuttle" class="normal-text">@lang('messages.Airport Shuttle')</div>
                                                    <div id="extraBedText" class="normal-text">@lang('messages.Extra Bed')</div>
                                                    <hr class="form-hr">
                                                    <div class="total-price">@lang('messages.Total Price')</div>
                                                </div>
                                                <div class="col-6 col-md-6 text-right">
                                                    <div id="suitesAndVillasPrice" class="text-price"><span id="suitesAndVillasPriceLable"></span></div>
                                                    <div id="airportShuttlePrice" class="text-price"><span id="airportShuttleText"></span></div>
                                                    <div id="extraBedPrice" class="text-price"><span id='extraBedPriceTotal'>{{ currencyFormatUsd($final_price) }}</span></div>
                                                    <hr class="form-hr">
                                                    <div class="total-price"><span id="finalprice">{{ currencyFormatUsd($final_price) }}</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </section>

                                    <input type="hidden" name="orderno" value="{{ $orderNumber }}">
                                    <input type="hidden" name="airport_shuttle_in_price_id" id="airport_shuttle_in_price_id" value="">
                                    <input type="hidden" name="airport_shuttle_out_price_id" id="airport_shuttle_out_price_id" value="">
                                    <input type="hidden" name="duration" id="duration" value="{{ $duration }}">
                                    <input type="hidden" name="final_price" id="final_price" value="{{ $final_price }}">
                                    <input type="hidden" name="var_package_price" id='var_package_price' value="{{ $final_price }}">
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
