@extends('layouts.head')

@section('title', __('messages.Hotels'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/hotels/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/hotels/forms.js') }}" defer></script>
@endpush

@php
    $galleryImages = $hotels->images ?? collect();
    $galleryCount = $hotels->images_count ?? $galleryImages->count();
@endphp

@section('content')
    @can('isAdmin')
        <div class="mobile-menu-overlay"></div>
        <main class="main-container hotel-form-page hotel-gallery-page">
            <div class="pd-ltr-20">
                <x-backend.page-hero
                    class="hotel-form-hero"
                    eyebrow="Hotel Media"
                    title="Edit Gallery - {{ $hotels->name }}"
                    description="Manage compact Hotel gallery assets without changing Hotel profile, pricing, room, promo, booking, order, or reservation data."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.hotels.show', $hotels->id) }}#profile" class="backend-page-primary-action">
                            <i class="fa fa-arrow-left"></i>
                            Back to Detail
                        </a>
                    </x-slot>
                </x-backend.page-hero>

                <x-backend.breadcrumb-toolbar
                    class="hotel-form-toolbar"
                    :items="[
                        ['label' => 'Admin Panel', 'url' => route('admin.panel-main.view')],
                        ['label' => 'Hotel Manager', 'url' => route('admin.hotels.index')],
                        ['label' => $hotels->name, 'url' => route('admin.hotels.show', $hotels->id)],
                    ]"
                    current="Edit Gallery"
                >
                    <x-slot name="actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ $galleryCount }} images</span>
                    </x-slot>
                </x-backend.breadcrumb-toolbar>

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

                <x-backend.detail-layout class="hotel-gallery-layout">
                    <x-slot name="main">
                        <section class="backend-panel backend-form-panel hotel-form-panel">
                            <div class="backend-section-header">
                                <div>
                                    <span class="backend-section-header__label">Current Gallery</span>
                                    <h2>Gallery Images</h2>
                                </div>
                                <p>Review active Hotel gallery images in a compact thumbnail grid.</p>
                            </div>

                            <div class="backend-form-panel__body">
                                @if ($galleryImages->count() > 0)
                                    <div class="hotel-gallery-grid">
                                        @foreach ($galleryImages as $img)
                                            <article class="hotel-gallery-card">
                                                <a href="{{ asset('storage/hotels/hotels-galery/' . $img->image) }}" class="hotel-gallery-card__preview" target="_blank" rel="noopener">
                                                    <img src="{{ asset('storage/hotels/hotels-galery/' . $img->image) }}" alt="{{ $hotels->name }} gallery image" loading="lazy" decoding="async">
                                                </a>
                                                <div class="hotel-gallery-card__body">
                                                    <span>Image #{{ $loop->iteration }}</span>
                                                    <strong title="{{ $img->image }}">{{ $img->image }}</strong>
                                                </div>
                                                <div class="hotel-gallery-card__actions">
                                                    <a href="{{ asset('storage/hotels/hotels-galery/' . $img->image) }}" class="backend-icon-action backend-icon-action--view" target="_blank" rel="noopener" aria-label="Preview gallery image">
                                                        <i class="fa fa-eye"></i>
                                                    </a>
                                                    <form action="{{ route('admin.hotels.images.destroy', [$hotels->id, $img->id]) }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                        <button type="submit" class="backend-icon-action backend-icon-action--delete is-danger" aria-label="Delete gallery image">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="backend-empty-state backend-empty-state--compact">
                                        <i class="fa fa-picture-o"></i>
                                        <strong>No gallery images yet.</strong>
                                        <span>Upload Hotel gallery images from the media upload panel.</span>
                                    </div>
                                @endif
                            </div>
                        </section>

                        <section class="backend-panel backend-form-panel hotel-form-panel">
                            <div class="backend-section-header">
                                <div>
                                    <span class="backend-section-header__label">Add New Images</span>
                                    <h2>Upload Gallery</h2>
                                </div>
                                <p>Select one or more validated image files. Uploading gallery images does not update Hotel profile fields.</p>
                            </div>

                            <form class="backend-form" action="{{ route('admin.hotels.gallery.store', $hotels->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="backend-form-panel__body">
                                    <div class="backend-form-field is-wide">
                                        <label for="hotelGalleryImages">Gallery Images</label>
                                        <input
                                            type="file"
                                            name="images[]"
                                            id="hotelGalleryImages"
                                            class="backend-form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror"
                                            accept="image/jpeg,image/png,image/jpg,image/webp"
                                            multiple
                                            required
                                            data-hotel-gallery-input
                                            data-hotel-gallery-preview-target="[data-hotel-gallery-preview]"
                                            data-hotel-gallery-status-target="[data-hotel-gallery-status]"
                                        >
                                        <small class="hotel-file-status" data-hotel-gallery-status>No gallery files selected</small>
                                        @error('images')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                        @error('images.*')
                                            <span class="invalid-feedback d-block">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="hotel-gallery-upload-preview" data-hotel-gallery-preview aria-live="polite"></div>

                                    <div class="backend-form-actions">
                                        <a href="{{ route('admin.hotels.show', $hotels->id) }}#profile" class="backend-button backend-button-secondary">
                                            <i class="fa fa-times"></i>
                                            Cancel
                                        </a>
                                        <button type="submit" class="backend-button backend-button-primary">
                                            <i class="fa fa-upload"></i>
                                            Upload Images
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </section>
                    </x-slot>

                    <x-slot name="side">
                        <section class="backend-panel backend-detail-side-card">
                            <div class="backend-section-header">
                                <div>
                                    <span class="backend-section-header__label">Hotel Context</span>
                                    <h2>{{ $hotels->name }}</h2>
                                </div>
                                <p>Read-only context for the media currently being managed.</p>
                            </div>
                            <div class="backend-detail-side-card__body">
                                <dl class="backend-detail-side-list">
                                    <div>
                                        <dt>Status</dt>
                                        <dd>{{ $hotels->status ?: '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Region</dt>
                                        <dd>{{ $hotels->region ?: '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Address</dt>
                                        <dd>{{ $hotels->address ?: '-' }}</dd>
                                    </div>
                                    <div>
                                        <dt>Total Images</dt>
                                        <dd>{{ $galleryCount }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </section>

                        <section class="backend-panel backend-detail-side-card">
                            <div class="backend-section-header">
                                <div>
                                    <span class="backend-section-header__label">Current Cover</span>
                                    <h2>Primary Image</h2>
                                </div>
                                <p>The cover image is managed from the Hotel edit form.</p>
                            </div>
                            <div class="backend-detail-side-card__body">
                                @if ($hotels->cover)
                                    <figure class="hotel-gallery-cover-context">
                                        <img src="{{ asset('storage/hotels/hotels-cover/' . $hotels->cover) }}" alt="{{ $hotels->name }} cover image" loading="lazy" decoding="async">
                                    </figure>
                                @else
                                    <p class="hotel-gallery-guidance-copy">No cover image available.</p>
                                @endif
                            </div>
                        </section>

                        <section class="backend-panel backend-detail-side-card">
                            <div class="backend-section-header">
                                <div>
                                    <span class="backend-section-header__label">Media Guidance</span>
                                    <h2>Upload Rules</h2>
                                </div>
                                <p>Use gallery images that can represent the property clearly.</p>
                            </div>
                            <div class="backend-detail-side-card__body">
                                <ul class="backend-detail-side-list">
                                    <li>Accepted formats: JPG, JPEG, PNG, WEBP.</li>
                                    <li>Maximum file size: 4 MB per image.</li>
                                    <li>Landscape images work best for Hotel public listings.</li>
                                    <li>Images are uploaded only after the form is submitted.</li>
                                </ul>
                            </div>
                        </section>

                        <section class="backend-panel backend-detail-side-card">
                            <div class="backend-section-header">
                                <div>
                                    <span class="backend-section-header__label">Related Actions</span>
                                    <h2>Hotel Management</h2>
                                </div>
                            </div>
                            <div class="backend-detail-side-actions">
                                <a href="{{ route('admin.hotels.show', $hotels->id) }}#profile" class="backend-button backend-button-secondary">
                                    <i class="fa fa-arrow-left"></i>
                                    Back to Hotel
                                </a>
                                <a href="{{ route('admin.hotels.edit', $hotels->id) }}" class="backend-button backend-button-secondary">
                                    <i class="fa fa-pencil"></i>
                                    Edit Hotel
                                </a>
                                <a href="{{ route('admin.hotels.room.create', $hotels->id) }}" class="backend-button backend-button-secondary">
                                    <i class="fa fa-bed"></i>
                                    Add Room
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
