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
                    eyebrow="Hotel Gallery"
                    title="Edit Gallery - {{ $hotels->name }}"
                    description="Manage gallery images using the shared backend form standard."
                >
                    <x-slot name="action">
                        <a href="{{ route('admin.hotels.show', $hotels->id) }}#profile" class="backend-page-primary-action">
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
                            <li class="breadcrumb-item"><a href="{{ route('admin.hotels.show', $hotels->id) }}">{{ $hotels->name }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Edit Gallery</li>
                        </ol>
                    </nav>
                    <div class="backend-page-toolbar__actions">
                        <span class="backend-status-badge backend-status-badge--info">{{ $hotels->images->count() }} images</span>
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
                                    <span class="backend-section-header__label">Existing Gallery</span>
                                    <h2>Gallery Images</h2>
                                </div>
                            </div>
                            <div class="hotel-form-panel__body">
                                @if ($hotels->images->count() > 0)
                                    <div class="hotel-form-gallery-grid">
                                        @foreach ($hotels->images as $img)
                                            <div class="hotel-form-gallery-item">
                                                <form action="{{ route('admin.hotels.images.destroy', $img->id) }}" method="post" class="hotel-form-gallery-delete">
                                                    @csrf
                                                    @method('delete')
                                                    <button type="submit" class="backend-icon-action is-danger" aria-label="Delete gallery image">
                                                        <i class="fa fa-times"></i>
                                                    </button>
                                                </form>
                                                <img src="{{ asset('storage/hotels/hotels-galery/' . $img->image) }}" class="hotel-form-gallery-image" alt="{{ $hotels->name }} gallery image">
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="backend-empty-state backend-empty-state--compact">
                                        <i class="fa fa-picture-o"></i>
                                        <strong>No gallery images yet.</strong>
                                        <span>Upload multiple images from the form panel.</span>
                                    </div>
                                @endif
                            </div>
                        </section>
                    </div>

                    <aside class="hotel-form-sidebar">
                        <section class="backend-panel hotel-form-panel">
                            <div class="backend-section-header hotel-form-panel__heading">
                                <div>
                                    <span class="backend-section-header__label">Upload</span>
                                    <h2>Add Images</h2>
                                </div>
                            </div>
                            <form action="{{ route('func.hotel.edit', $hotels->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="hotel-form-panel__body">
                                    <div class="backend-form-field">
                                        <label for="images">Gallery Images</label>
                                        <div class="dropzone mt-1 text-center pd-20">
                                            <div class="images-preview-div"></div>
                                        </div>
                                        <input type="file" name="images[]" id="images" class="@error('images[]') is-invalid @enderror" value="{{ $hotels->images }}" multiple>
                                        @error('images[]')
                                            <div class="backend-alert backend-alert--danger">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <input type="hidden" name="name" value="{{ $hotels->name }}">
                                    <input type="hidden" name="web" value="{{ $hotels->web }}">
                                    <input type="hidden" name="region" value="{{ $hotels->region }}">
                                    <input type="hidden" name="contract" value="{{ $hotels->contract }}">
                                    <input type="hidden" name="address" value="{{ $hotels->address }}">
                                    <input type="hidden" name="contact_person" value="{{ $hotels->contact_person }}">
                                    <input type="hidden" name="description" value="{{ $hotels->description }}">
                                    <input type="hidden" name="facility" value="{{ $hotels->facility }}">
                                    <input type="hidden" name="note" value="{{ $hotels->note }}">
                                    <input type="hidden" name="phone" value="{{ $hotels->phone }}">
                                    <input type="hidden" name="status" value="{{ $hotels->status }}">
                                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                    <input type="hidden" name="cover" value="{{ $hotels->cover }}">

                                    <div class="backend-form-actions">
                                        <a href="{{ route('admin.hotels.show', $hotels->id) }}#profile" class="backend-button backend-button-secondary">
                                            <i class="fa fa-times"></i>
                                            Cancel
                                        </a>
                                        <button type="submit" class="backend-button backend-button-primary">
                                            <i class="fa fa-floppy-o"></i>
                                            Update Gallery
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </section>
                    </aside>
                </div>

                @include('layouts.footer')
            </div>
        </main>
    @endcan
@endsection
