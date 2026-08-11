@extends('layouts.head')

@section('title', __('messages.Activity'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/activities/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/activities/forms.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container activity-form-page activity-form-page--create">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    eyebrow="Operations Inventory"
                    title="Add Activity"
                    description="Create a new activity product with partner ownership, operational capacity, pricing, validity, and publishing details."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.activities.index') }}" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Activities
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar activity-form-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.activities.index') }}">Activities</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Add Activity</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--draft">Draft by default</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('error'))
                    <section class="backend-feedback activity-form-feedback">
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

                        @if (session()->has('error'))
                            <div class="backend-alert backend-alert--danger">
                                <strong>{{ session('error') }}</strong>
                            </div>
                        @endif
                    </section>
                @endif

                <form id="activityCreateForm" action="{{ route('admin.activities.store') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <section class="backend-panel activity-form-panel">
                        <div class="backend-section-header">
                            <div>
                                <span class="backend-section-header__label">Profile</span>
                                <h2>Activity Information</h2>
                            </div>
                            <p>Use the shared backend form pattern so activity inventory stays visually consistent with the admin dashboard standard.</p>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="backend-form-field">
                                    <label for="cover" class="backend-form-label">Cover Image <span>*</span></label>
                                    <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" data-activity-file-input data-activity-file-input-target="#activityCoverFileStatus" required>
                                    <span id="activityCoverFileStatus" class="activity-file-status" data-activity-file-input-default="No cover selected">No cover selected</span>
                                    @error('cover')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="backend-form-field">
                                    <label for="name" class="backend-form-label">Name <span>*</span></label>
                                    <input type="text" id="name" name="name" class="backend-form-control @error('name') is-invalid @enderror" placeholder="Insert activity name" value="{{ old('name') }}" required>
                                    @error('name')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="backend-form-field">
                                    <label for="partners_id" class="backend-form-label">Partner <span>*</span></label>
                                    <select id="partners_id" name="partners_id" class="backend-form-control @error('partners_id') is-invalid @enderror" required>
                                        <option value="">Select Partner</option>
                                        @foreach ($partners as $partner)
                                            <option value="{{ $partner->id }}" @selected((string) old('partners_id') === (string) $partner->id)>{{ $partner->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('partners_id')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="backend-form-field">
                                    <label for="type" class="backend-form-label">Type <span>*</span></label>
                                    <select id="type" name="type" class="backend-form-control @error('type') is-invalid @enderror" required>
                                        <option value="">Select Type</option>
                                        @foreach ($type as $activityType)
                                            <option value="{{ $activityType->type }}" @selected(old('type') === $activityType->type)>{{ $activityType->type }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="backend-form-field">
                                    <label for="location" class="backend-form-label">Location <span>*</span></label>
                                    <input type="text" id="location" name="location" class="backend-form-control @error('location') is-invalid @enderror" placeholder="Activity location" value="{{ old('location') }}" required>
                                    @error('location')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="backend-form-field">
                                    <label for="map" class="backend-form-label">Map</label>
                                    <input type="text" id="map" name="map" class="backend-form-control @error('map') is-invalid @enderror" placeholder="Google Maps link" value="{{ old('map') }}">
                                    @error('map')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="backend-panel activity-form-panel">
                        <div class="backend-section-header">
                            <div>
                                <span class="backend-section-header__label">Operations</span>
                                <h2>Capacity and Pricing</h2>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="backend-form-field">
                                    <label for="duration" class="backend-form-label">Duration <span>*</span></label>
                                    <select id="duration" name="duration" class="backend-form-control @error('duration') is-invalid @enderror" required>
                                        <option value="">Select Duration</option>
                                        @foreach (['15 Minutes', '30 Minutes', '1 Hour', '2 Hours', '3 Hours', '4 Hours', '5 Hours', '6 Hours', '7 Hours', '8 Hours', '9 Hours', '10 Hours'] as $duration)
                                            <option value="{{ $duration }}" @selected(old('duration') === $duration)>{{ $duration === '10 Hours' ? 'Full Day (10 hours)' : $duration }}</option>
                                        @endforeach
                                    </select>
                                    @error('duration')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="backend-form-field">
                                    <label for="validity" class="backend-form-label">Valid Until <span>*</span></label>
                                    <input type="text" id="validity" name="validity" value="{{ old('validity') }}" class="backend-form-control date-picker @error('validity') is-invalid @enderror" placeholder="Select date" required>
                                    <p class="backend-form-help">The Activity will automatically return to Draft after this date.</p>
                                    @error('validity')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="backend-form-field">
                                    <label for="min_pax" class="backend-form-label">Minimum Order <span>*</span></label>
                                    <input type="number" id="min_pax" name="min_pax" value="{{ old('min_pax') }}" class="backend-form-control @error('min_pax') is-invalid @enderror" placeholder="Minimum pax" required>
                                    @error('min_pax')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="backend-form-field">
                                    <label for="qty" class="backend-form-label">Capacity <span>*</span></label>
                                    <input type="number" id="qty" name="qty" value="{{ old('qty') }}" class="backend-form-control @error('qty') is-invalid @enderror" placeholder="Maximum pax" required>
                                    @error('qty')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="backend-form-field">
                                    <label for="contract_rate" class="backend-form-label">Contract Rate IDR <span>*</span></label>
                                    <input type="number" id="contract_rate" name="contract_rate" class="backend-form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ old('contract_rate') }}" required>
                                    @error('contract_rate')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-12 col-md-6">
                                <div class="backend-form-field">
                                    <label for="markup" class="backend-form-label">Markup USD</label>
                                    <input type="number" id="markup" name="markup" class="backend-form-control @error('markup') is-invalid @enderror" placeholder="Insert markup" value="{{ old('markup') }}">
                                    @error('markup')
                                        <span class="invalid-feedback d-block">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="backend-panel activity-form-panel">
                        <div class="backend-section-header">
                            <div>
                                <span class="backend-section-header__label">Content</span>
                                <h2>Customer-Facing Copy</h2>
                            </div>
                        </div>

                        <div class="backend-form-field">
                            <label for="description" class="backend-form-label">Description <span>*</span></label>
                            <textarea id="description" name="description" class="textarea_editor backend-form-control border-radius-0 @error('description') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert description" required>{{ old('description') }}</textarea>
                            @error('description')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="backend-form-field">
                            <label for="itinerary" class="backend-form-label">Itinerary</label>
                            <textarea id="itinerary" name="itinerary" class="textarea_editor backend-form-control border-radius-0 @error('itinerary') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert itinerary">{{ old('itinerary') }}</textarea>
                            @error('itinerary')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="backend-form-field">
                            <label for="include" class="backend-form-label">Include</label>
                            <textarea id="include" name="include" class="textarea_editor backend-form-control border-radius-0 @error('include') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert inclusions">{{ old('include') }}</textarea>
                            @error('include')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="backend-form-field">
                            <label for="cancellation_policy" class="backend-form-label">Cancellation Policy</label>
                            <textarea id="cancellation_policy" name="cancellation_policy" class="textarea_editor backend-form-control border-radius-0 @error('cancellation_policy') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert cancellation policy">{{ old('cancellation_policy') }}</textarea>
                            @error('cancellation_policy')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="backend-form-field">
                            <label for="additional_info" class="backend-form-label">Additional Information</label>
                            <textarea id="additional_info" name="additional_info" class="textarea_editor backend-form-control border-radius-0 @error('additional_info') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert additional information">{{ old('additional_info') }}</textarea>
                            @error('additional_info')
                                <span class="invalid-feedback d-block">{{ $message }}</span>
                            @enderror
                        </div>

                        <input class="backend-form-control" id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                        <input id="page" name="page" value="add-activity" type="hidden">

                        <div class="backend-page-toolbar backend-form-actions">
                            <div class="backend-page-toolbar__actions">
                                <a href="{{ route('admin.activities.index') }}" class="backend-button backend-button-secondary">Cancel</a>
                                <button type="submit" class="backend-button backend-button-primary">
                                    <i class="fa fa-check"></i>
                                    Add Activity
                                </button>
                            </div>
                        </div>
                    </section>
                </form>

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
