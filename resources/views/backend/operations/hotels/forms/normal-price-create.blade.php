@extends('layouts.head')

@section('title', __('messages.Hotels'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/hotels/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/hotels/forms.js') }}" defer></script>
@endpush

@section('content')
    @canany(['posDev','posAuthor'])
        <div class="mobile-menu-overlay"></div>
        <main class="main-container hotel-form-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="hotel-form-hero"
                    eyebrow="Rate Plan"
                    title="Add Normal Price"
                    description="Create regular room price rows for {{ $hotels->name }} using the canonical backend Create layout."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.hotels.show', $hotels->id) }}#normalPrice" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Detail
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <x-backend.breadcrumb-toolbar
                    class="hotel-form-toolbar"
                    :items="[
                        ['label' => 'Admin Panel', 'url' => route('admin.panel-main.view')],
                        ['label' => 'Hotel Manager', 'url' => route('admin.hotels.index')],
                        ['label' => $hotels->name, 'url' => route('admin.hotels.show', $hotels->id)],
                    ]"
                    current="Add Normal Price"
                >
                    <x-slot name="actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ $rooms->count() }} rooms available</span>
                    </x-slot>
                </x-backend.breadcrumb-toolbar>

                @if ($errors->any() || session()->has('success') || session()->has('invalid') || session()->has('error'))
                    <section class="backend-feedback hotel-form-feedback">
                        @if ($errors->any())
                            <div class="backend-alert backend-alert--danger">
                                <strong>Action needs attention.</strong>
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session()->has('success'))
                            <div class="backend-alert backend-alert--success">
                                <strong>{{ session('success') }}</strong>
                            </div>
                        @endif

                        @if (session()->has('invalid') || session()->has('error'))
                            <div class="backend-alert backend-alert--danger">
                                <strong>{{ session('invalid') ?? session('error') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <form id="hotelNormalPriceCreate" class="backend-form" action="{{ route('admin.hotels.normal-prices.store') }}" method="post" data-hotel-price-repeater>
                    @csrf
                    <input name="hotels_id" value="{{ $hotels->id }}" type="hidden">
                    <input name="hotel_context" value="{{ $hotelContext }}" type="hidden">

                    <x-backend.detail-layout>
                        <x-slot name="main">
                            <section class="backend-panel backend-form-panel hotel-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Hotel & Room</span>
                                        <h2>Normal Price Rows</h2>
                                    </div>
                                    <p>Select Hotel rooms and assign the period plus master price inputs for each row.</p>
                                    <div class="backend-page-toolbar__actions">
                                        <button type="button" class="backend-button backend-button-secondary" data-hotel-price-add>
                                            <i class="fa fa-plus"></i>
                                            Add More
                                        </button>
                                    </div>
                                </div>

                                <div class="backend-form-panel__body">
                                    <div class="hotel-form-price-list" data-hotel-price-list>
                                        @include('backend.operations.hotels.forms.partials.normal-price-row')
                                    </div>

                                    <template data-hotel-price-template>
                                        @include('backend.operations.hotels.forms.partials.normal-price-row')
                                    </template>
                                </div>
                            </section>
                        </x-slot>

                        <x-slot name="side">
                            <section class="backend-panel backend-detail-side-card">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Hotel Context</span>
                                        <h2>{{ $hotels->name }}</h2>
                                    </div>
                                    <p>Read-only parent Hotel for these normal price rows.</p>
                                </div>
                                <div class="backend-detail-side-card__body">
                                    <dl class="backend-detail-side-list">
                                        <div>
                                            <dt>Region</dt>
                                            <dd>{{ $hotels->region ?: '-' }}</dd>
                                        </div>
                                        <div>
                                            <dt>Available Rooms</dt>
                                            <dd>{{ $rooms->count() }}</dd>
                                        </div>
                                    </dl>
                                </div>
                                <div class="backend-detail-side-actions">
                                    <a href="{{ route('admin.hotels.show', $hotels->id) }}#normalPrice" class="backend-button backend-button-secondary">
                                        <i class="fa fa-building"></i>
                                        View Existing Prices
                                    </a>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Price Period</span>
                                        <h2>Validity Rules</h2>
                                    </div>
                                    <p>This rate applies only within the configured start and end date.</p>
                                </div>
                                <ul class="backend-detail-side-list">
                                    <li>
                                        <span>Room Relation</span>
                                        <strong>Room must belong to this Hotel</strong>
                                        <small>The server rejects manipulated Hotel and Room combinations.</small>
                                    </li>
                                    <li>
                                        <span>Overlap Rule</span>
                                        <strong>No overlapping normal price</strong>
                                        <small>Normal orders require exactly one authoritative rate for every stay night.</small>
                                    </li>
                                </ul>
                            </section>

                            <section class="backend-panel backend-detail-side-card">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Pricing Context</span>
                                        <h2>Master Inputs</h2>
                                    </div>
                                    <p>Store only source pricing values here; published accommodation totals are calculated server-side when needed.</p>
                                </div>
                                <ul class="backend-detail-side-list">
                                    <li>
                                        <span>Contract Rate</span>
                                        <strong>IDR supplier/base rate</strong>
                                    </li>
                                    <li>
                                        <span>Markup</span>
                                        <strong>USD markup input</strong>
                                    </li>
                                    <li>
                                        <span>Kick Back</span>
                                        <strong>USD post-published-rate adjustment</strong>
                                    </li>
                                </ul>
                            </section>
                        </x-slot>
                    </x-backend.detail-layout>

                    <section class="backend-page-toolbar backend-form-actions hotel-form-actions">
                        <div class="backend-page-toolbar__actions">
                            <a href="{{ route('admin.hotels.show', $hotels->id) }}#normalPrice" class="backend-button backend-button-secondary">
                                <i class="fa fa-times"></i>
                                Cancel
                            </a>
                            <button type="submit" class="backend-button backend-button-primary">
                                <i class="fa fa-floppy-o"></i>
                                Save Price
                            </button>
                        </div>
                    </section>
                </form>
            </div>
        </main>
    @endcanany
@endsection
