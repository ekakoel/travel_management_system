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
                    title="Edit Normal Price"
                    description="Update the regular price period and rate for {{ $hotels->name }} without leaving the standardized Hotel workspace."
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
                            <li class="breadcrumb-item"><a href="{{ route('admin.hotels.index') }}">Hotel Manager</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.hotels.show', $hotels->id) }}">{{ $hotels->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Normal Price</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ $price->rooms?->rooms ?: 'Room price' }}</span>
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
                        <section class="backend-panel hotel-form-panel">
                            <div class="backend-section-header hotel-form-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Normal Price</span>
                                    <h2>Price Details</h2>
                                </div>
                            </div>

                            <form id="hotelNormalPriceUpdate" action="{{ route('admin.hotels.normal-prices.update', $price->id) }}" method="post">
                                @csrf
                                @method('put')
                                <div class="hotel-form-panel__body">
                                    <div class="backend-form-grid backend-form-grid--compact">
                                        <div class="backend-form-field is-wide">
                                            <label for="rooms_id">Room <b>*</b></label>
                                            <select class="backend-form-control" id="rooms_id" name="rooms_id" required>
                                                @foreach ($rooms as $sroom)
                                                    <option value="{{ $sroom->id }}" @selected((int) old('rooms_id', $price->rooms_id) === (int) $sroom->id)>{{ $sroom->rooms }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="start_date">Start Date</label>
                                            <input name="start_date" id="start_date" class="backend-form-control date-picker" placeholder="Select date" type="text" value="{{ old('start_date', dateFormat($price->start_date)) }}" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="end_date">End Date</label>
                                            <input name="end_date" id="end_date" class="backend-form-control date-picker" placeholder="Select date" type="text" value="{{ old('end_date', dateFormat($price->end_date)) }}" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="contract_rate">Contract Rate</label>
                                            <input class="backend-form-control" type="number" id="contract_rate" name="contract_rate" value="{{ old('contract_rate', $price->contract_rate) }}" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="markup">Markup</label>
                                            <input class="backend-form-control" type="number" id="markup" name="markup" value="{{ old('markup', $price->markup) }}" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="kick_back">Kick Back</label>
                                            <input class="backend-form-control" type="number" id="kick_back" name="kick_back" value="{{ old('kick_back', $price->kick_back ?? 0) }}">
                                        </div>
                                    </div>

                                    <input class="backend-form-control" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                    <input class="backend-form-control" name="hotels_id" value="{{ $hotels->id }}" type="hidden">

                                    <div class="backend-form-actions">
                                        <a href="{{ route('admin.hotels.show', $hotels->id) }}#normalPrice" class="backend-button backend-button-secondary">
                                            <i class="fa fa-times"></i>
                                            Cancel
                                        </a>
                                        <button type="submit" class="backend-button backend-button-primary">
                                            <i class="fa fa-floppy-o"></i>
                                            Update Price
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
                                    <h2>Rate Notes</h2>
                                </div>
                            </div>
                            <div class="hotel-form-panel__body">
                                <span class="backend-status-badge backend-status-badge--info">Existing price row</span>
                                <p class="backend-form-help">Changing the period or room assignment affects availability calculations for this normal price row.</p>
                            </div>
                        </section>
                    </aside>
                </div>
            </div>
        </main>
    @endcan
@endsection
