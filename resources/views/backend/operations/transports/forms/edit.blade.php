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
        @php
            $statusTone = match (strtolower((string) $transport->status)) {
                'active' => 'active',
                'archived' => 'muted',
                'removed', 'rejected', 'invalid' => 'danger',
                default => 'draft',
            };
        @endphp

        <div class="mobile-menu-overlay"></div>
        <main class="main-container transport-form-admin-page transport-form-admin-page--edit">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    eyebrow="Operations Inventory"
                    title="Edit {{ $transport->name }}"
                    description="Update transport profile, operational capacity, customer-facing content, and publication status."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.transports.show', $transport->id) }}" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Detail
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <x-backend.breadcrumb-toolbar
                    class="transport-form-toolbar"
                    :items="[
                        ['label' => 'Admin Panel', 'url' => route('admin.panel-main.view')],
                        ['label' => 'Transportation', 'url' => route('admin.transports.index')],
                        ['label' => $transport->name, 'url' => route('admin.transports.show', $transport->id)],
                    ]"
                    current="Edit Transport"
                >
                    <x-slot name="actions">
                        <span class="backend-status-badge backend-status-badge--{{ $statusTone }}">{{ $transport->status }}</span>
                    </x-slot>
                </x-backend.breadcrumb-toolbar>

                @include('backend.operations.transports.partials.form-feedback')

                <form id="updateTransportForm" class="backend-form" data-transport-form action="{{ route('admin.transports.update', $transport->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('put')

                    <x-backend.detail-layout class="transport-edit-layout">
                        <x-slot name="main">
                            <section class="backend-panel backend-form-panel transport-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Cover / Media</span>
                                        <h2>Cover Image</h2>
                                    </div>
                                    <p>Keep the current cover or choose a replacement. The new image is uploaded only when the form is saved.</p>
                                </div>

                                <div class="backend-form-panel__body">
                                    <div class="backend-form-field is-wide">
                                        <label for="cover" class="backend-form-label">Current Cover</label>
                                        <div class="transport-form-cover-control">
                                            <figure class="transport-form-cover-preview" data-transport-cover-preview>
                                                @if ($transport->cover)
                                                    <img src="{{ asset('storage/transports/transports-cover/' . $transport->cover) }}" alt="{{ $transport->name }}" loading="lazy">
                                                @endif
                                            </figure>
                                            <div class="transport-form-cover-input">
                                                <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" accept="image/jpeg,image/png,image/jpg,image/webp" data-transport-cover-input>
                                                <span class="transport-file-status" data-transport-cover-status data-transport-file-input-default="Keep existing cover">Keep existing cover</span>
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
                                    <p>Core information used to identify, categorize, and assign this Transport.</p>
                                </div>

                                <div class="backend-form-panel__body">
                                    <div class="backend-form-grid">
                                        <div class="backend-form-field">
                                            <label for="name" class="backend-form-label">Transport Name</label>
                                            <input type="text" id="name" name="name" class="backend-form-control @error('name') is-invalid @enderror" value="{{ old('name', $transport->name) }}" maxlength="255" required>
                                            @error('name')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="backend-form-field">
                                            <label for="partner_id" class="backend-form-label">Partner Provider</label>
                                            <select id="partner_id" name="partner_id" class="backend-form-control @error('partner_id') is-invalid @enderror">
                                                <option value="">No partner selected</option>
                                                @foreach ($partners as $partner)
                                                    <option value="{{ $partner->id }}" @selected((string) old('partner_id', $transport->partner_id) === (string) $partner->id)>{{ $partner->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('partner_id')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="backend-form-field">
                                            <label for="type" class="backend-form-label">Transport Type</label>
                                            <select id="type" name="type" class="backend-form-control @error('type') is-invalid @enderror" required>
                                                <option value="">Select type</option>
                                                @foreach ($type as $transportType)
                                                    <option value="{{ $transportType->type }}" @selected(old('type', $transport->type) === $transportType->type)>{{ $transportType->type }}</option>
                                                @endforeach
                                            </select>
                                            @error('type')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="backend-form-field">
                                            <label for="brand" class="backend-form-label">Vehicle Brand</label>
                                            <select id="brand" name="brand" class="backend-form-control @error('brand') is-invalid @enderror" required>
                                                <option value="">Select brand</option>
                                                @foreach ($brand as $transportBrand)
                                                    <option value="{{ $transportBrand->brand }}" @selected(old('brand', $transport->brand) === $transportBrand->brand)>{{ $transportBrand->brand }}</option>
                                                @endforeach
                                            </select>
                                            @error('brand')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel backend-form-panel transport-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Operational Information</span>
                                        <h2>Capacity and Inventory</h2>
                                    </div>
                                    <p>Update the passenger capacity and public inventory configuration.</p>
                                </div>
                                <div class="backend-form-panel__body">
                                    <div class="backend-form-grid">
                                        <div class="backend-form-field">
                                            <label for="capacity" class="backend-form-label">Capacity</label>
                                            <input type="number" id="capacity" name="capacity" class="backend-form-control @error('capacity') is-invalid @enderror" value="{{ old('capacity', $transport->capacity) }}" min="1" required>
                                            @error('capacity')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                                        </div>
                                        <div class="backend-form-field">
                                            <label for="inventory" class="backend-form-label">Public Inventory</label>
                                            <input type="number" id="inventory" name="inventory" class="backend-form-control @error('inventory') is-invalid @enderror" value="{{ old('inventory', $transport->inventory) }}" min="0">
                                            <p class="backend-form-help">Leave empty to use the service default.</p>
                                            @error('inventory')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel backend-form-panel transport-form-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Content</span>
                                        <h2>Customer-Facing Information</h2>
                                    </div>
                                    <p>Maintain the Transport content displayed to customers.</p>
                                </div>
                                <div class="backend-form-panel__body">
                                    <div class="backend-form-grid">
                                        @foreach ([
                                            ['name' => 'description', 'label' => 'Description', 'required' => true, 'placeholder' => 'Insert description'],
                                            ['name' => 'include', 'label' => 'Include', 'required' => true, 'placeholder' => 'Insert inclusions'],
                                            ['name' => 'cancellation_policy', 'label' => 'Cancellation Policy', 'required' => false, 'placeholder' => 'Insert cancellation policy'],
                                            ['name' => 'additional_info', 'label' => 'Additional Information', 'required' => false, 'placeholder' => 'Insert additional information'],
                                        ] as $field)
                                            <div class="backend-form-field is-wide">
                                                <label for="{{ $field['name'] }}" class="backend-form-label">{{ $field['label'] }}</label>
                                                <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="backend-form-control @error($field['name']) is-invalid @enderror" data-backend-richtext="true" placeholder="{{ $field['placeholder'] }}" @required($field['required'])>{{ old($field['name'], $transport->{$field['name']}) }}</textarea>
                                                @error($field['name'])<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </section>
                            <section class="backend-page-toolbar backend-form-actions transport-form-actions">
                                <div class="backend-page-toolbar__actions"><a href="{{ route('admin.transports.show', $transport->id) }}" class="backend-button backend-button-secondary"><i class="fa fa-times"></i> Cancel</a><button type="submit" class="backend-button backend-button-primary"><i class="fa fa-check"></i> Save Changes</button></div>
                            </section>
                        </x-slot>

                        <x-slot name="side">
                            <section class="backend-panel backend-detail-side-card transport-edit-context-panel">
                                <div class="backend-section-header">
                                    <div><span class="backend-section-header__label">Current Status</span><h2><span class="backend-status-badge backend-status-badge--{{ $statusTone }}">{{ $transport->status }}</span></h2></div>
                                    <p>Manage the publication lifecycle of this Transport.</p>
                                </div>
                                <div class="backend-detail-side-card__body">
                                    <div class="backend-form-field">
                                        <label for="status" class="backend-form-label">Status</label>
                                        <select id="status" name="status" class="backend-form-control @error('status') is-invalid @enderror" required>
                                            @foreach (['Active', 'Draft', 'Archived'] as $status)
                                                <option value="{{ $status }}" @selected(old('status', $transport->status) === $status)>{{ $status }}</option>
                                            @endforeach
                                        </select>
                                        @error('status')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </section>

                            <section class="backend-panel backend-detail-side-card transport-edit-context-panel">
                                <div class="backend-section-header"><div><span class="backend-section-header__label">Transport Context</span><h2>Master Data</h2></div><p>Current values selected from the Transport master data lists.</p></div>
                                <div class="backend-detail-side-card__body"><dl class="backend-detail-side-list"><div><dt>Type</dt><dd>{{ $transport->type ?: '-' }}</dd></div><div><dt>Brand</dt><dd>{{ $transport->brand ?: '-' }}</dd></div><div><dt>Partner</dt><dd>{{ $transport->partner?->name ?: '-' }}</dd></div></dl></div>
                            </section>

                            <section class="backend-panel backend-detail-side-card transport-edit-context-panel">
                                <div class="backend-section-header"><div><span class="backend-section-header__label">Record Information</span><h2>Metadata</h2></div><p>Administrative record details.</p></div>
                                <div class="backend-detail-side-card__body"><dl class="backend-detail-side-list"><div><dt>Author</dt><dd>{{ $transport->user?->name ?: 'User #' . $transport->author_id }}</dd></div><div><dt>Created</dt><dd>{{ $transport->created_at?->format('d M Y H:i') ?: '-' }}</dd></div><div><dt>Updated</dt><dd>{{ $transport->updated_at?->format('d M Y H:i') ?: '-' }}</dd></div></dl></div>
                            </section>

                            <section class="backend-panel backend-detail-side-card transport-edit-context-panel">
                                <div class="backend-section-header"><div><span class="backend-section-header__label">Operational Summary</span><h2>Capacity</h2></div><p>Context for operational readiness.</p></div>
                                <div class="backend-detail-side-card__body"><dl class="backend-detail-side-list"><div><dt>Seats</dt><dd>{{ $transport->capacity ?: '-' }}</dd></div><div><dt>Inventory</dt><dd>{{ $transport->inventory ?? 'Service default' }}</dd></div><div><dt>Price Rows</dt><dd>{{ number_format($transport->prices_count) }}</dd></div></dl></div>
                            </section>

                            <section class="backend-panel backend-detail-side-card transport-edit-context-panel">
                                <div class="backend-section-header"><div><span class="backend-section-header__label">Actions</span><h2>Related Management</h2></div></div>
                                <div class="backend-detail-side-actions"><a href="{{ route('admin.transports.show', $transport->id) }}" class="backend-button backend-button-secondary"><i class="fa fa-eye"></i> View Transport</a><a href="{{ route('admin.transports.gallery.edit', $transport->id) }}" class="backend-button backend-button-secondary"><i class="fa fa-picture-o"></i> Manage Gallery</a></div>
                            </section>
                        </x-slot>
                    </x-backend.detail-layout>
                </form>

                @include('layouts.footer')
            </div>
        </main>
    @endcanany
@endsection