@extends('layouts.head')

@section('title', __('messages.Activity'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/activities/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/activities/forms.js') }}" defer></script>
@endpush

@php
    $galleryImages = $activities->images ?? collect();
@endphp

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container activity-gallery-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    eyebrow="Operations Inventory"
                    title="Edit Activity Gallery"
                    description="Manage gallery assets for {{ $activities->name }} using the shared backend workspace pattern."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.activities.show', $activities->id) }}" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Detail
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar activity-gallery-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('admin.panel-main.view') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.activities.index') }}">Activities</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.activities.show', $activities->id) }}">{{ $activities->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Gallery</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ $galleryImages->count() }} Images</span>
                    </div>
                </section>

                @if ($errors->any() || session()->has('success') || session()->has('error'))
                    <section class="backend-feedback activity-gallery-feedback">
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

                <x-backend.detail-layout class="activity-gallery-layout activity-gallery-layout--editor">
                    <x-slot name="main">
                        <section class="backend-panel activity-gallery-panel">
                            <div class="backend-section-header activity-gallery-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Gallery</span>
                                    <h2>Current Images</h2>
                                </div>
                                <p>Review each gallery image and remove only the image that should no longer be displayed.</p>
                            </div>

                            @if ($galleryImages->count() > 0)
                                <div class="activity-gallery-manager">
                                    @foreach ($galleryImages as $img)
                                        <article class="activity-gallery-manager__item">
                                            <a href="{{ $img->imageUrl() }}" class="activity-gallery-manager__thumb" target="_blank" rel="noopener">
                                                <img src="{{ $img->imageUrl() }}" alt="{{ $activities->name }} gallery image" loading="lazy">
                                            </a>
                                            <div class="activity-gallery-manager__body">
                                                <strong>{{ basename((string) $img->image) }}</strong>
                                                <span>{{ $img->created_at ? $img->created_at->format('d M Y H:i') : 'Stored gallery image' }}</span>
                                            </div>
                                            <div class="activity-gallery-manager__actions">
                                                <form action="{{ route('admin.activities.images.destroy', $img->id) }}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="backend-button backend-button-danger" data-activity-gallery-delete="{{ $activities->name }}" aria-label="Delete gallery image for {{ $activities->name }}">
                                                        <i class="fa fa-trash-alt"></i>
                                                        Delete Image
                                                    </button>
                                                </form>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div class="backend-empty-state activity-gallery-empty">
                                    <i class="fa fa-picture-o"></i>
                                    <strong>No gallery images.</strong>
                                    <span>Upload images to enrich this activity detail page.</span>
                                </div>
                            @endif
                        </section>

                        <form
                            action="{{ route('admin.gallery-activities.update', $activities->id) }}"
                            method="POST"
                            enctype="multipart/form-data"
                            class="backend-form activity-gallery-upload-form"
                        >
                            @csrf
                            @method('PUT')

                            <section class="backend-panel activity-gallery-panel activity-gallery-upload-panel">
                                <div class="backend-section-header activity-gallery-panel__heading">
                                    <div>
                                        <span class="backend-section-header__label">Upload</span>
                                        <h2>Add Gallery Images</h2>
                                    </div>
                                    <p>Upload JPG, JPEG, PNG, or WEBP gallery images without changing Activity content.</p>
                                </div>

                                <div class="activity-gallery-upload">
                                    <div class="backend-form-field">
                                        <label for="images" class="backend-form-label">
                                            Gallery Images
                                        </label>

                                        <input
                                            type="file"
                                            name="images[]"
                                            id="images"
                                            class="backend-form-control
                                                @error('images') is-invalid @enderror
                                                @error('images.*') is-invalid @enderror"
                                            data-activity-file-input
                                            data-activity-file-input-target="#activityGalleryFileStatus"
                                            data-activity-gallery-preview-target="#activityGalleryPreview"
                                            accept=".jpg,.jpeg,.png,.webp"
                                            multiple
                                        >

                                        <span
                                            id="activityGalleryFileStatus"
                                            class="activity-file-status"
                                            data-activity-file-input-default="No gallery images selected"
                                        >
                                            No gallery images selected
                                        </span>

                                        @error('images')
                                            <span class="invalid-feedback d-block">
                                                {{ $message }}
                                            </span>
                                        @enderror

                                        @error('images.*')
                                            <span class="invalid-feedback d-block">
                                                {{ $message }}
                                            </span>
                                        @enderror
                                    </div>

                                    <div id="activityGalleryPreview" class="activity-gallery-preview" data-activity-gallery-preview>
                                        <div class="activity-gallery-preview__empty">
                                            <i class="fa fa-images"></i>
                                            <span>No selected images to preview.</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="backend-page-toolbar backend-form-actions">
                                    <div class="backend-page-toolbar__actions">
                                        <a
                                            href="{{ route('admin.activities.show', $activities->id) }}"
                                            class="backend-button backend-button-secondary"
                                        >
                                            Cancel
                                        </a>

                                        <button
                                            type="submit"
                                            class="backend-button backend-button-primary"
                                        >
                                            <i class="fa fa-check"></i>
                                            Update Gallery
                                        </button>
                                    </div>
                                </div>
                            </section>
                        </form>
                    </x-slot>

                    <x-slot name="side">
                        <section class="backend-panel backend-detail-side-card activity-gallery-context-panel">
                            <div class="backend-section-header">
                                <div>
                                    <span class="backend-section-header__label">Gallery Context</span>
                                    <h2>{{ $activities->name }}</h2>
                                </div>
                                <p>Operational media summary for this Activity.</p>
                            </div>

                            <ul class="backend-detail-side-list">
                                <li>
                                    <span>Status</span>
                                    <strong>{{ $activities->status ?: '-' }}</strong>
                                    <small>Current Activity publication state.</small>
                                </li>
                                <li>
                                    <span>Gallery Images</span>
                                    <strong>{{ number_format($galleryImages->count()) }}</strong>
                                    <small>Total gallery images currently stored.</small>
                                </li>
                                <li>
                                    <span>Cover Image</span>
                                    <strong>{{ $activities->cover ? 'Available' : 'Missing' }}</strong>
                                    <small>Cover image remains managed from the Activity edit page.</small>
                                </li>
                                <li>
                                    <span>Validity</span>
                                    <strong>{{ $activities->validity ? dateFormat($activities->validity) : '-' }}</strong>
                                    <small>Last public travel date allowed for this Activity.</small>
                                </li>
                            </ul>

                            <div class="backend-detail-side-actions">
                                <a href="{{ route('admin.activities.show', $activities->id) }}" class="backend-page-primary-action">
                                    <i class="fa fa-arrow-left"></i>
                                    Back to Detail
                                </a>
                                <a href="{{ route('admin.activities.edit', $activities->id) }}" class="backend-toolbar-action">
                                    <i class="fa fa-pencil-alt"></i>
                                    Edit Activity
                                </a>
                            </div>
                        </section>
                    </x-slot>
                </x-backend.detail-layout>

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
