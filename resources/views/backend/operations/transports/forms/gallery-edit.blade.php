@extends('layouts.head')

@section('title', __('messages.Transports'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/transports/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/transports/forms.js') }}" defer></script>
@endpush

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container transport-gallery-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    eyebrow="Operations Inventory"
                    title="Edit Transport Gallery"
                    description="Manage gallery assets for {{ $transports->name }} using the shared backend workspace pattern."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.transports.show', $transports->id) }}" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Detail
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <section class="backend-page-toolbar transport-gallery-toolbar">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.transports.index') }}">Transportation</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.transports.show', $transports->id) }}">{{ $transports->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Gallery</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ $transports->images->count() }} Images</span>
                    </div>
                </section>

                @include('backend.operations.transports.partials.form-feedback')

                <div class="row">
                    <div class="col-12 col-lg-7">
                        <section class="backend-panel transport-gallery-panel">
                            <div class="backend-section-header">
                                <div>
                                    <span class="backend-section-header__label">Gallery</span>
                                    <h2>Current Images</h2>
                                </div>
                                <p>Review and remove images that should no longer appear in this transport gallery.</p>
                            </div>

                            @if ($transports->images->count() > 0)
                                <div class="transport-gallery-grid">
                                    @foreach ($transports->images as $img)
                                        <article class="backend-table-card transport-gallery-card">
                                            <img class="img-fluid" src="{{ asset('storage/transports/transports-gallery/' . $img->image) }}" alt="{{ $transports->name }}" loading="lazy">
                                            <div class="backend-table-actions">
                                                <form action="{{ route('admin.transports.images.destroy', $img->id) }}" method="post">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="backend-icon-action is-danger" data-transport-gallery-delete="{{ $transports->name }}" aria-label="Delete gallery image for {{ $transports->name }}">
                                                        <i class="fa fa-trash-o"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            @else
                                <div class="backend-empty-state">
                                    <i class="fa fa-picture-o"></i>
                                    <strong>No gallery images.</strong>
                                    <span>Upload images to enrich this transport detail page.</span>
                                </div>
                            @endif
                        </section>
                    </div>

                    <div class="col-12 col-lg-5">
                        <form data-transport-form action="{{ route('admin.transports.update', $transports->id) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')

                            <section class="backend-panel transport-gallery-panel">
                                <div class="backend-section-header">
                                    <div>
                                        <span class="backend-section-header__label">Upload</span>
                                        <h2>Add Gallery Images</h2>
                                    </div>
                                    <p>Profile fields are carried as hidden values so gallery updates do not overwrite existing transport data.</p>
                                </div>

                                @include('backend.operations.transports.partials.profile-hidden-fields', ['transport' => $transports])

                                <label class="backend-form-field is-wide">
                                    <span>Gallery Images</span>
                                    <input type="file" name="images[]" id="images" class="backend-form-control @error('images[]') is-invalid @enderror" data-transport-gallery-input data-transport-file-input-target="#transportGalleryFileStatus" multiple>
                                    <small id="transportGalleryFileStatus" class="transport-file-status" data-transport-file-input-default="No gallery images selected">No gallery images selected</small>
                                    @error('images[]')
                                        <small class="backend-form-error">{{ $message }}</small>
                                    @enderror
                                </label>

                                <div class="transport-gallery-preview" data-transport-gallery-preview aria-live="polite"></div>

                                <div class="backend-page-toolbar backend-form-actions">
                                    <div class="backend-page-toolbar__actions">
                                        <a href="{{ route('admin.transports.show', $transports->id) }}" class="backend-button backend-button-secondary">Cancel</a>
                                        <button type="submit" class="backend-button backend-button-primary">
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
