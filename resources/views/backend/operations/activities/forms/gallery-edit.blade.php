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
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.activities.index') }}">Activities</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.activities.show', $activities->id) }}">{{ $activities->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Gallery</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ $activities->images->count() }} Images</span>
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

                <div class="row">
                    <div class="col-12 col-lg-7">
                        <section class="backend-panel activity-gallery-panel">
                            <div class="backend-section-header">
                                <div>
                                    <span class="backend-section-header__label">Gallery</span>
                                    <h2>Current Images</h2>
                                </div>
                                <p>Delete images that should no longer be part of this activity gallery.</p>
                            </div>

                            @if ($activities->images->count() > 0)
                                <div class="row">
                                    @foreach ($activities->images as $img)
                                        <div class="col-12 col-md-6">
                                            <article class="backend-table-card activity-gallery-card">
                                                <img class="img-fluid" src="{{ asset('storage/' . $img->image) }}" alt="{{ $activities->name }}" loading="lazy">
                                                <div class="backend-table-actions">
                                                    <form action="{{ route('admin.activities.images.destroy', $img->id) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="backend-icon-action is-danger" data-activity-gallery-delete="{{ $activities->name }}" aria-label="Delete gallery image for {{ $activities->name }}">
                                                            <i class="fa fa-trash-o"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </article>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="backend-empty-state">
                                    <i class="fa fa-picture-o"></i>
                                    <strong>No gallery images.</strong>
                                    <span>Upload images to enrich this activity detail page.</span>
                                </div>
                            @endif
                        </section>
                    </div>

                    <div class="col-12 col-lg-5">
                        <form
                            action="{{ route('admin.gallery-activities.update', $activities->id) }}"
                            method="POST"
                            enctype="multipart/form-data"
                        >
                            @csrf
                            @method('PUT')

                            <section class="backend-panel activity-gallery-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Upload</span>
                                        <h2>Add Gallery Images</h2>
                                    </div>

                                    <p>
                                        Upload new images without changing the existing activity information.
                                    </p>
                                </div>

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
                    </div>
                </div>

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
