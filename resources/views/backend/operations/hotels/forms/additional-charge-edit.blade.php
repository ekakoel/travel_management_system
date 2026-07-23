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
                    eyebrow="Additional Charge"
                    title="Edit Additional Charge"
                    description="Update charge rules, mandatory period, pricing, and multilingual descriptions for {{ $hotel->name }}."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.hotels.show', $hotel->id) }}#additional-charge" class="backend-page-primary-action">
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
                            <li class="breadcrumb-item"><a href="{{ route('admin.hotels.show', $hotel->id) }}">{{ $hotel->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Additional Charge</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--{{ $additionalCharge->mandatory ? 'warning' : 'info' }}">{{ $additionalCharge->mandatory ? 'Mandatory' : 'Optional' }}</span>
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
                                    <span class="backend-section-header__label">Charge Details</span>
                                    <h2>Additional Charge</h2>
                                </div>
                            </div>

                            <form id="hotelAdditionalChargeUpdate" action="{{ route('admin.hotels.additional-charges.update', $additionalCharge->id) }}" method="post">
                                @csrf
                                @method('put')
                                <div class="hotel-form-panel__body">
                                    <div class="backend-form-grid backend-form-grid--compact">
                                        <div class="backend-form-field">
                                            <label for="type">Type <b>*</b></label>
                                            <select class="backend-form-control" id="type" name="type" required>
                                                @foreach (['Per Guest', 'Per Booking', 'Per Room', 'Per Night'] as $type)
                                                    <option value="{{ $type }}" @selected(old('type', $additionalCharge->type) === $type)>{{ $type }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="name">Name <b>*</b></label>
                                            <input class="backend-form-control" id="name" name="name" value="{{ old('name', $additionalCharge->name) }}" placeholder="Charge name" type="text" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="mandatory">Mandatory <b>*</b></label>
                                            <select class="backend-form-control" id="mandatory" name="mandatory" required>
                                                <option value="0" @selected((int) old('mandatory', $additionalCharge->mandatory) === 0)>No</option>
                                                <option value="1" @selected((int) old('mandatory', $additionalCharge->mandatory) === 1)>Yes</option>
                                            </select>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="mandatory_start">Mandatory Date Start</label>
                                            <input id="mandatory_start" name="mandatory_start" class="backend-form-control date-picker" value="{{ old('mandatory_start', $additionalCharge->mandatory_start ? dateFormat($additionalCharge->mandatory_start) : '') }}" type="text">
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="mandatory_end">Mandatory Date End</label>
                                            <input id="mandatory_end" name="mandatory_end" class="backend-form-control date-picker" value="{{ old('mandatory_end', $additionalCharge->mandatory_end ? dateFormat($additionalCharge->mandatory_end) : '') }}" type="text">
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="contract_rate">Contract Rate <b>*</b></label>
                                            <input class="backend-form-control" id="contract_rate" name="contract_rate" value="{{ old('contract_rate', $additionalCharge->contract_rate) }}" min="0" type="number" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="markup">Markup <b>*</b></label>
                                            <input class="backend-form-control" id="markup" name="markup" value="{{ old('markup', $additionalCharge->markup) }}" min="0" type="number" required>
                                        </div>

                                        @foreach ([
                                            'description' => 'Description',
                                            'description_traditional' => 'Description - Chinese Traditional',
                                            'description_simplified' => 'Description - Chinese Simplified',
                                        ] as $field => $label)
                                            <div class="backend-form-field is-wide">
                                                <label for="{{ $field }}">{{ $label }}</label>
                                                <textarea class="backend-form-control" id="{{ $field }}" name="{{ $field }}" data-backend-richtext="true">{{ old($field, $additionalCharge->{$field}) }}</textarea>
                                            </div>
                                        @endforeach
                                    </div>

                                    <input class="backend-form-control" name="hotel_id" value="{{ $hotel->id }}" type="hidden">
                                    <input class="backend-form-control" name="service_id" value="{{ $hotel->id }}" type="hidden">
                                    <input class="backend-form-control" name="author" value="{{ Auth::user()->id }}" type="hidden">

                                    <div class="backend-form-actions">
                                        <a href="{{ route('admin.hotels.show', $hotel->id) }}#additional-charge" class="backend-button backend-button-secondary">
                                            <i class="fa fa-times"></i>
                                            Cancel
                                        </a>
                                        <button type="submit" class="backend-button backend-button-primary">
                                            <i class="fa fa-floppy-o"></i>
                                            Update Charge
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </section>
                    </div>

                    <aside class="hotel-form-sidebar">
                        <section class="backend-panel hotel-form-panel">
                            <div class="backend-section-header hotel-form-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Guide</span>
                                    <h2>Charge Notes</h2>
                                </div>
                            </div>
                            <div class="hotel-form-panel__body">
                                <span class="backend-status-badge backend-status-badge--{{ $additionalCharge->mandatory ? 'warning' : 'info' }}">{{ $additionalCharge->mandatory ? 'Mandatory' : 'Optional' }}</span>
                                <p class="backend-form-help">Mandatory charges should include a valid start and end date. Optional charges can leave mandatory dates empty.</p>
                            </div>
                        </section>
                    </aside>
                </div>

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
