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
                    eyebrow="Package Price"
                    title="Edit Package"
                    description="Update package availability, room assignment, duration, price, status, and copy for {{ $hotel->name }}."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.hotels.show', $hotel->id) }}#package" class="backend-page-primary-action">
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
                            <li class="breadcrumb-item"><a href="{{ route('admin.hotels.show', $hotel->id) }}">{{ $hotel->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Package</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--{{ strtolower($package->status) === 'active' ? 'active' : 'draft' }}">{{ $package->status }}</span>
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
                                    <span class="backend-section-header__label">Bundled Offer</span>
                                    <h2>Package Details</h2>
                                </div>
                            </div>

                            <form id="hotelPackageUpdate" action="{{ route('admin.hotels.packages.update', $package->id) }}" method="post">
                                @csrf
                                @method('put')
                                <div class="hotel-form-panel__body">
                                    <div class="backend-form-grid backend-form-grid--compact">
                                        <div class="backend-form-field">
                                            <label for="name">Package Name <b>*</b></label>
                                            <input class="backend-form-control" id="name" name="name" value="{{ old('name', $package->name) }}" placeholder="Package name" type="text" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="status">Status <b>*</b></label>
                                            <select class="backend-form-control" id="status" name="status" required>
                                                @foreach (['Active', 'Draft'] as $status)
                                                    <option value="{{ $status }}" @selected(old('status', $package->status) === $status)>{{ $status }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="rooms_id">Room <b>*</b></label>
                                            <select class="backend-form-control" id="rooms_id" name="rooms_id" required>
                                                @foreach ($rooms as $room)
                                                    <option value="{{ $room->id }}" @selected((int) old('rooms_id', $package->rooms_id) === (int) $room->id)>{{ $room->rooms }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="booking_code">Booking Code</label>
                                            <input class="backend-form-control" id="booking_code" name="booking_code" value="{{ old('booking_code', $package->booking_code) }}" type="text">
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="duration">Minimum Stay <b>*</b></label>
                                            <input class="backend-form-control" id="duration" name="duration" value="{{ old('duration', $package->duration) }}" min="1" type="number" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="stay_period_start">Stay Period Start <b>*</b></label>
                                            <input id="stay_period_start" name="stay_period_start" class="backend-form-control date-picker" value="{{ old('stay_period_start', dateFormat($package->stay_period_start)) }}" type="text" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="stay_period_end">Stay Period End <b>*</b></label>
                                            <input id="stay_period_end" name="stay_period_end" class="backend-form-control date-picker" value="{{ old('stay_period_end', dateFormat($package->stay_period_end)) }}" type="text" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="contract_rate">Contract Rate <b>*</b></label>
                                            <input class="backend-form-control" id="contract_rate" name="contract_rate" value="{{ old('contract_rate', $package->contract_rate) }}" min="0" type="number" required>
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="markup">Markup <b>*</b></label>
                                            <input class="backend-form-control" id="markup" name="markup" value="{{ old('markup', $package->markup) }}" min="0" type="number" required>
                                        </div>

                                    </div>
                                    <div class="backend-form-field-2">
                                        @foreach ([
                                            'benefits' => 'Benefits',
                                            'benefits_traditional' => 'Benefits - Chinese Traditional',
                                            'benefits_simplified' => 'Benefits - Chinese Simplified',
                                        ] as $field => $label)
                                        <div class="backend-form-field">
                                            <label for="{{ $field }}">{{ $label }}</label>
                                            <textarea class="backend-form-control" id="{{ $field }}" name="{{ $field }}" data-backend-richtext="true">{{ old($field, $package->{$field}) }}</textarea>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="backend-form-field-2">
                                        @foreach ([
                                            'include' => 'Inclusion',
                                            'include_traditional' => 'Inclusion - Chinese Traditional',
                                            'include_simplified' => 'Inclusion - Chinese Simplified',
                                        ] as $field => $label)
                                        <div class="backend-form-field">
                                            <label for="{{ $field }}">{{ $label }}</label>
                                            <textarea class="backend-form-control" id="{{ $field }}" name="{{ $field }}" data-backend-richtext="true">{{ old($field, $package->{$field}) }}</textarea>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="backend-form-field-2">
                                        @foreach ([
                                            'additional_info' => 'Additional Information',
                                            'additional_info_traditional' => 'Additional Information - Chinese Traditional',
                                            'additional_info_simplified' => 'Additional Information - Chinese Simplified',
                                        ] as $field => $label)
                                        <div class="backend-form-field">
                                            <label for="{{ $field }}">{{ $label }}</label>
                                            <textarea class="backend-form-control" id="{{ $field }}" name="{{ $field }}" data-backend-richtext="true">{{ old($field, $package->{$field}) }}</textarea>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="backend-form-field-2">
                                        @foreach ([
                                            'cancellation_policy' => 'Cancellation Policy',
                                            'cancellation_policy_traditional' => 'Cancellation Policy - Chinese Traditional',
                                            'cancellation_policy_simplified' => 'Cancellation Policy - Chinese Simplified',
                                        ] as $field => $label)
                                        <div class="backend-form-field">
                                            <label for="{{ $field }}">{{ $label }}</label>
                                            <textarea class="backend-form-control" id="{{ $field }}" name="{{ $field }}" data-backend-richtext="true">{{ old($field, $package->{$field}) }}</textarea>
                                        </div>
                                        @endforeach
                                    </div>

                                    <input class="backend-form-control" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                    <input class="backend-form-control" name="author" value="{{ Auth::user()->id }}" type="hidden">

                                    <div class="backend-form-actions">
                                        <a href="{{ route('admin.hotels.show', $hotel->id) }}#package" class="backend-button backend-button-secondary">
                                            <i class="fa fa-times"></i>
                                            Cancel
                                        </a>
                                        <button type="submit" class="backend-button backend-button-primary">
                                            <i class="fa fa-floppy-o"></i>
                                            Update Package
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
                                    <h2>Package Notes</h2>
                                </div>
                            </div>
                            <div class="hotel-form-panel__body">
                                <span class="backend-status-badge backend-status-badge--{{ strtolower($package->status) === 'active' ? 'active' : 'draft' }}">{{ $package->status }}</span>
                                <p class="backend-form-help">Package pricing multiplies contract rate by duration before markup and tax are calculated. Keep duration aligned with stay period rules.</p>
                            </div>
                        </section>
                    </aside>
                </div>

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
