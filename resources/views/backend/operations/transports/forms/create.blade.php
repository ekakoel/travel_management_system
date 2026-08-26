@extends('layouts.head')

@section('title', __('messages.Transports'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/transports/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/transports/forms.js') }}" defer></script>
@endpush

@section('content')
    @canany(['posDev', 'posAuthor'])
        <div class="mobile-menu-overlay"></div>
        <main class="main-container transport-form-admin-page transport-form-admin-page--create">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    eyebrow="Operations Inventory"
                    title="Add Transportation"
                    description="Create a transport inventory item with its profile, capacity, cover, and customer-facing content."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.transports.index') }}" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Transports
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <x-backend.breadcrumb-toolbar
                    class="transport-form-toolbar"
                    :items="[
                        ['label' => 'Admin Panel', 'url' => route('admin.panel-main.view')],
                        ['label' => 'Transportation', 'url' => route('admin.transports.index')],
                    ]"
                    current="Add Transport"
                >
                    <x-slot name="actions">
                        <span class="backend-status-badge backend-status-badge--draft">Draft by default</span>
                    </x-slot>
                </x-backend.breadcrumb-toolbar>

                @include('backend.operations.transports.partials.form-feedback')

                <form id="createTransportForm" class="backend-form" data-transport-form action="{{ route('admin.transports.store') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <x-backend.detail-layout class="transport-create-layout">
                        <x-slot name="main">
                            <section class="backend-panel backend-form-panel transport-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Cover / Media</span>
                                        <h2>Cover Image</h2>
                                    </div>
                                    <p>Upload the primary image used for this Transport in backend and public views.</p>
                                </div>

                                <div class="backend-form-panel__body">
                                    <div class="backend-form-field is-wide">
                                        <label for="cover" class="backend-form-label">Cover Image</label>
                                        <div class="transport-form-cover-control">
                                            <figure class="transport-form-cover-preview" data-transport-cover-preview></figure>
                                            <div class="transport-form-cover-input">
                                                <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" data-transport-cover-input required>
                                                <span class="transport-file-status" data-transport-cover-status data-transport-file-input-default="No cover selected">No cover selected</span>
                                                @error('cover')
                                                    <span class="invalid-feedback d-block">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel backend-form-panel transport-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Basic Information</span>
                                        <h2>Transport Profile</h2>
                                    </div>
                                    <p>Core information used to identify and categorize this Transport inventory item.</p>
                                </div>

                                <div class="backend-form-panel__body">
                                    <div class="backend-form-grid">
                                        <div class="backend-form-field">
                                            <label for="name" class="backend-form-label">Transport Name</label>
                                            <input type="text" id="name" name="name" class="backend-form-control @error('name') is-invalid @enderror" placeholder="Insert transport name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="partner_id" class="backend-form-label">Partner Provider</label>
                                            <select id="partner_id" name="partner_id" class="backend-form-control @error('partner_id') is-invalid @enderror">
                                                <option value="">No partner selected</option>
                                                @foreach ($partners as $partner)
                                                    <option value="{{ $partner->id }}" @selected((string) old('partner_id') === (string) $partner->id)>{{ $partner->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('partner_id')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="type" class="backend-form-label">Transport Type</label>
                                            <select id="type" name="type" class="backend-form-control @error('type') is-invalid @enderror" required>
                                                <option value="">Select type</option>
                                                @foreach ($type as $transportType)
                                                    <option value="{{ $transportType->type }}" @selected(old('type') === $transportType->type)>{{ $transportType->type }}</option>
                                                @endforeach
                                            </select>
                                            @error('type')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="brand" class="backend-form-label">Vehicle Brand</label>
                                            <select id="brand" name="brand" class="backend-form-control @error('brand') is-invalid @enderror" required>
                                                <option value="">Select brand</option>
                                                @foreach ($brand as $transportBrand)
                                                    <option value="{{ $transportBrand->brand }}" @selected(old('brand') === $transportBrand->brand)>{{ $transportBrand->brand }}</option>
                                                @endforeach
                                            </select>
                                            @error('brand')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel backend-form-panel transport-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Capacity / Configuration</span>
                                        <h2>Operating Capacity</h2>
                                    </div>
                                    <p>Define the seats and public inventory available for this Transport.</p>
                                </div>

                                <div class="backend-form-panel__body">
                                    <div class="backend-form-grid">
                                        <div class="backend-form-field">
                                            <label for="capacity" class="backend-form-label">Capacity</label>
                                            <input type="number" id="capacity" name="capacity" class="backend-form-control @error('capacity') is-invalid @enderror" placeholder="Insert capacity" value="{{ old('capacity') }}" min="1" required>
                                            @error('capacity')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field">
                                            <label for="inventory" class="backend-form-label">Public Inventory</label>
                                            <input type="number" id="inventory" name="inventory" class="backend-form-control @error('inventory') is-invalid @enderror" placeholder="Optional inventory override" value="{{ old('inventory') }}" min="0">
                                            <p class="backend-form-help">Leave empty to use the service default.</p>
                                            @error('inventory')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel backend-form-panel transport-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Content / Translation</span>
                                        <h2>Customer-Facing Content</h2>
                                    </div>
                                    <p>Provide the customer-facing content stored by the Transport profile.</p>
                                </div>

                                <div class="backend-form-panel__body">
                                    <div class="backend-form-grid">
                                        <div class="backend-form-field is-wide">
                                            <label for="description" class="backend-form-label">Description</label>
                                            <textarea id="description" name="description" class="backend-form-control @error('description') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert description" required>{{ old('description') }}</textarea>
                                            @error('description')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field is-wide">
                                            <label for="include" class="backend-form-label">Include</label>
                                            <textarea id="include" name="include" class="backend-form-control @error('include') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert inclusions" required>{{ old('include') }}</textarea>
                                            @error('include')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field is-wide">
                                            <label for="cancellation_policy" class="backend-form-label">Cancellation Policy</label>
                                            <textarea id="cancellation_policy" name="cancellation_policy" class="backend-form-control @error('cancellation_policy') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert cancellation policy">{{ old('cancellation_policy') }}</textarea>
                                            @error('cancellation_policy')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>

                                        <div class="backend-form-field is-wide">
                                            <label for="additional_info" class="backend-form-label">Additional Information</label>
                                            <textarea id="additional_info" name="additional_info" class="backend-form-control @error('additional_info') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert additional information">{{ old('additional_info') }}</textarea>
                                            @error('additional_info')
                                                <span class="invalid-feedback d-block">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </section>
                            <section class="backend-page-toolbar backend-form-actions transport-form-actions">
                                <div class="backend-page-toolbar__actions">
                                    <a href="{{ route('admin.transports.index') }}" class="backend-button backend-button-secondary">Cancel</a>
                                    <button type="submit" class="backend-button backend-button-primary">
                                        <i class="fa fa-check"></i>
                                        Add Transportation
                                    </button>
                                </div>
                            </section>
                        </x-slot>

                        <x-slot name="side">
                            <section class="backend-panel backend-detail-side-card transport-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Initial Status</span>
                                        <h2><span class="backend-status-badge backend-status-badge--draft">Draft</span></h2>
                                    </div>
                                    <p>New Transport records are created as Draft by the server.</p>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card transport-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Operational Guidance</span>
                                        <h2>Before Save</h2>
                                    </div>
                                    <p>Check the operational fields before creating the inventory record.</p>
                                </div>
                                <ul class="backend-detail-side-list">
                                    <li>
                                        <span>Capacity</span>
                                        <strong>Use the vehicle seat capacity</strong>
                                        <small>Capacity must be at least one seat and is used by Transport operations.</small>
                                    </li>
                                    <li>
                                        <span>Inventory</span>
                                        <strong>Set only when an override is needed</strong>
                                        <small>Leave Public Inventory empty to use the service default.</small>
                                    </li>
                                </ul>
                            </section>

                            <section class="backend-panel backend-detail-side-card transport-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Pricing Context</span>
                                        <h2>Managed Separately</h2>
                                    </div>
                                    <p>Transport rates are added and maintained from the Transport detail page using the canonical pricing flow.</p>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card transport-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Publishing Guidance</span>
                                        <h2>Before Activation</h2>
                                    </div>
                                </div>
                                <ul class="backend-detail-side-list">
                                    <li>
                                        <span>Required</span>
                                        <strong>Complete profile, cover, capacity, and core content</strong>
                                        <small>Review the detail page and add applicable prices before activating the Transport.</small>
                                    </li>
                                </ul>
                            </section>

                            <section class="backend-panel backend-detail-side-card transport-create-context-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Actions</span>
                                        <h2>Transport Inventory</h2>
                                    </div>
                                </div>
                                <div class="backend-detail-side-actions">
                                    <a href="{{ route('admin.transports.index') }}" class="backend-button backend-button-secondary">Back to Transports</a>
                                </div>
                            </section>
                        </x-slot>
                    </x-backend.detail-layout>

                </form>

                @include('layouts.footer')
            </div>
        </main>
    @endcanany
@endsection