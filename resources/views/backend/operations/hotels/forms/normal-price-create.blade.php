@extends('layouts.head')

@section('title', __('messages.Hotels'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/hotels/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/hotels/forms.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container hotel-form-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="hotel-form-hero"
                    eyebrow="Rate Plan"
                    title="Add Normal Price"
                    description="Create one or more regular room price rows for {{ $hotels->name }} using the standardized backend form layout."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.hotels.show', $hotels->id) }}#normalPrice" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Detail
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar hotel-form-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('hotels-admin.index') }}">Hotel Manager</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.hotels.show', $hotels->id) }}">{{ $hotels->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Normal Price</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ $rooms->count() }} rooms available</span>
                    </div>
                </section>

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

                <div class="hotel-form-layout">
                    <div class="hotel-form-main">
                        <section class="backend-panel hotel-form-panel" data-hotel-price-repeater>
                            <div class="backend-section-header hotel-form-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Normal Price</span>
                                    <h2>Price Rows</h2>
                                </div>
                                <div class="backend-page-toolbar__actions">
                                    <button type="button" class="backend-button backend-button-secondary" data-hotel-price-add>
                                        <i class="fa fa-plus"></i>
                                        Add More
                                    </button>
                                </div>
                            </div>

                            <form id="hotelNormalPriceCreate" action="{{ route('admin.hotels.normal-prices.store') }}" method="post">
                                @csrf
                                <div class="hotel-form-panel__body">
                                    <div class="hotel-form-price-list" data-hotel-price-list>
                                        @include('backend.operations.hotels.forms.partials.normal-price-row')
                                    </div>

                                    <template data-hotel-price-template>
                                        @include('backend.operations.hotels.forms.partials.normal-price-row')
                                    </template>

                                    <input class="backend-form-control" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                    <input class="backend-form-control" name="hotels_id" value="{{ $hotels->id }}" type="hidden">

                                    <div class="backend-form-actions">
                                        <a href="{{ route('admin.hotels.show', $hotels->id) }}#normalPrice" class="backend-button backend-button-secondary">
                                            <i class="fa fa-times"></i>
                                            Cancel
                                        </a>
                                        <button type="submit" class="backend-button backend-button-primary">
                                            <i class="fa fa-floppy-o"></i>
                                            Save Price
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </section>
                    </div>

                    <aside class="hotel-form-sidebar">
                        @include('admin.usd-rate')

                        <section class="backend-panel hotel-form-panel">
                            <div class="backend-section-header hotel-form-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Guide</span>
                                    <h2>Input Notes</h2>
                                </div>
                            </div>
                            <div class="hotel-form-panel__body">
                                <span class="backend-status-badge backend-status-badge--draft">Draft impact</span>
                                <p class="backend-form-help">Normal price rows make rooms sellable. Use Add More when multiple rooms share the same creation workflow, then verify active status on the hotel detail page.</p>
                            </div>
                        </section>
                    </aside>
                </div>
            </div>
        </main>
    @endcan
@endsection
