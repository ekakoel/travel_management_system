@extends('layouts.head')

@section('title', __('messages.Tour'))

@push('styles')
    <link rel="stylesheet" href="{{ mix('build/backend/css/operations/tours/forms.css') }}">
@endpush

@push('scripts')
    <script src="{{ mix('build/backend/js/operations/tours/forms.js') }}" defer></script>
@endpush

@section('content')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <main class="main-container tour-form-page">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <x-backend.page-hero
                        class="tour-form-hero"
                        eyebrow="Tour Package"
                        title="Edit Tour Package"
                        description="Update tour package content, status, cover image, and route locations using the shared backend form standard."
                    >
                        <x-slot name="action">
                            <a href="{{ route('admin.tours.show', $tour->id) }}" class="backend-page-primary-action">
                                <i class="fa fa-arrow-left"></i>
                                Back to Detail
                            </a>
                        </x-slot>
                    </x-backend.page-hero>

                    <section class="backend-page-toolbar tour-form-toolbar">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('view.admin-panel-main') }}">Admin Panel</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.tour-packages.index') }}">Tour Packages</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.tours.show', $tour->id) }}">{{ $tour->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit</li>
                            </ol>
                        </nav>
                        <div class="backend-page-toolbar__actions">
                            <span class="backend-status-badge backend-status-badge--{{ strtolower($tour->status ?? '') === 'active' ? 'active' : 'draft' }}">{{ $tour->status }}</span>
                        </div>
                    </section>

                    @if ($errors->any() || session()->has('success') || session()->has('error'))
                        <section class="backend-feedback tour-form-feedback">
                            @if ($errors->any())
                                <div class="backend-alert backend-alert--danger">
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
                        <div class="col-md-4 mobile">
                            <div class="row">
                                @include('admin.usd-rate')
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="backend-panel tour-form-panel">
                                <div class="backend-section-header tour-form-panel__heading">
                                    <div>
                                        <span class="backend-section-header__label">Tour Profile</span>
                                        <h2>{{ $tour->name }}</h2>
                                    </div>
                                </div>
                                <form id="updateTour{{ $tour->id }}" action="{{ route('admin.tours.update',$tour->id) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                                    <div class="row tour-form-panel__body">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="dropzone">
                                                        <div class="cover-preview-div">
                                                            <img src="{{ asset('storage/tours/tours-cover/'.$tour->cover)  }}" alt="{{ $tour->name }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="cover" class="form-label col-form-label">Cover Image </label>
                                                <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="cover" class="form-label col-form-label">Status </label>
                                                <select id="status" name="status" class="backend-form-control col-12 @error('status') is-invalid @enderror" required>
                                                    <option {{ $tour->status == "Active" ?"selected":""; }} value="Active">Active</option>
                                                    <option {{ $tour->status == "Draft" ?"selected":""; }} value="Draft">Draft</option>
                                                </select>
                                                @error('status')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-8">
                                            <div class="backend-form-field">
                                                <label for="name" class="backend-form-label">Tour Name</label>
                                                <input type="text" id="name" name="name" class="backend-form-control @error('name') is-invalid @enderror" placeholder="Insert tour package name" value="{{ $tour->name }}" required>
                                                @error('name')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="backend-form-field">
                                                <label for="code" class="backend-form-label">Tour Code</label>
                                                <input type="text" id="code" name="code" class="backend-form-control @error('code') is-invalid @enderror" placeholder="Insert tour code" value="{{ $tour->code }}" required>
                                                @error('code')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-8">
                                            <div class="backend-form-field">
                                                <label for="name_traditional" class="backend-form-label">Tour Name Traditional</label>
                                                <input type="text" id="name_traditional" name="name_traditional" class="backend-form-control @error('name_traditional') is-invalid @enderror" placeholder="Insert tour package name in traditional" value="{{ $tour->name_traditional }}" required>
                                                @error('name_traditional')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-8">
                                            <div class="backend-form-field">
                                                <label for="name_simplified" class="backend-form-label">Tour Name Simplified</label>
                                                <input type="text" id="name_simplified" name="name_simplified" class="backend-form-control @error('name_simplified') is-invalid @enderror" placeholder="Insert tour package name in simplified" value="{{ $tour->name_simplified }}" required>
                                                @error('name_simplified')
                                                    <div class="backend-form-error">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-4 col-md-4">
                                                    <div class="backend-form-field">
                                                        <label for="type" class="backend-form-label">Type <span> *</span></label>
                                                        <select id="type" name="type" class="backend-form-control col-12 @error('type') is-invalid @enderror" required>
                                                            @foreach ($types as $type)
                                                                <option {{ $tour->type_id == $type->id?"selected":"" }} selected value="{{ $type->id }}">{{ $type->type }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('type')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4 col-md-4">
                                                    <div class="backend-form-field">
                                                        <label for="duration_days" class="backend-form-label">Duration Days <span> *</span></label>
                                                        <select id="duration_days" name="duration_days" class="backend-form-control col-12 @error('duration_days') is-invalid @enderror" required>
                                                            <option {{ $tour->duration_days == 1?"selected":"" }} value="1">1D</option>
                                                            <option {{ $tour->duration_days == 2?"selected":"" }} value="2">2D</option>
                                                            <option {{ $tour->duration_days == 3?"selected":"" }} value="3">3D</option>
                                                            <option {{ $tour->duration_days == 4?"selected":"" }} value="4">4D</option>
                                                            <option {{ $tour->duration_days == 5?"selected":"" }} value="5">5D</option>
                                                            <option {{ $tour->duration_days == 6?"selected":"" }} value="6">6D</option>
                                                            <option {{ $tour->duration_days == 7?"selected":"" }} value="7">7D</option>
                                                        </select>
                                                        @error('duration_days')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4 col-md-4">
                                                    <div class="backend-form-field">
                                                        <label for="duration_nights" class="backend-form-label">Duration Nights <span> *</span></label>
                                                        <select id="duration_nights" name="duration_nights" class="backend-form-control col-12 @error('duration_nights') is-invalid @enderror" required>
                                                            <option {{ $tour->duration_nights == 0?"selected":"" }} value="0">-</option>
                                                            <option {{ $tour->duration_nights == 1?"selected":"" }} value="1">1N</option>
                                                            <option {{ $tour->duration_nights == 2?"selected":"" }} value="2">2N</option>
                                                            <option {{ $tour->duration_nights == 3?"selected":"" }} value="3">3N</option>
                                                            <option {{ $tour->duration_nights == 4?"selected":"" }} value="4">4N</option>
                                                            <option {{ $tour->duration_nights == 5?"selected":"" }} value="5">5N</option>
                                                            <option {{ $tour->duration_nights == 6?"selected":"" }} value="6">6N</option>
                                                            <option {{ $tour->duration_nights == 7?"selected":"" }} value="7">7N</option>
                                                        </select>
                                                        @error('duration_nights')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="short_description" class="form-label col-form-label">Short Description<span> *</span></label>
                                                        <textarea id="short_description" name="short_description" class="textarea_editor backend-form-control @error('short_description') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert short description" value="{{ $tour->short_description }}" required>{{ $tour->short_description }}</textarea>
                                                        @error('short_description')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="short_description_traditional" class="form-label col-form-label">Short Description Traditional<span> *</span></label>
                                                        <textarea id="short_description_traditional" name="short_description_traditional" class="textarea_editor backend-form-control @error('short_description_traditional') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert short description in Chinese traditional" value="{{ $tour->short_description_traditional }}" required>{{ $tour->short_description_traditional }}</textarea>
                                                        @error('short_description_traditional')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="short_description_simplified" class="form-label col-form-label">Short Description Simplified<span> *</span></label>
                                                        <textarea id="short_description_simplified" name="short_description_simplified" class="textarea_editor backend-form-control @error('short_description_simplified') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert short description in Chinese simplified" value="{{ $tour->short_description_simplified }}" required>{{ $tour->short_description_simplified }}</textarea>
                                                        @error('short_description_simplified')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="description" class="form-label col-form-label">Description</label>
                                                        <textarea id="description" name="description" class="textarea_editor backend-form-control @error('description') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert description" value="{{ $tour->description }}">{{ $tour->description }}</textarea>
                                                        @error('description')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="description_traditional" class="form-label col-form-label">Description Traditional</label>
                                                        <textarea id="description_traditional" name="description_traditional" class="textarea_editor backend-form-control @error('description_traditional') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert description in Chinese traditional" value="{{ $tour->description_traditional }}">{{ $tour->description_traditional }}</textarea>
                                                        @error('description_traditional')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="description_simplified" class="form-label col-form-label">Description Simplified</label>
                                                        <textarea id="description_simplified" name="description_simplified" class="textarea_editor backend-form-control @error('description_simplified') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert description in Chinese simplified" value="{{ $tour->description_simplified }}">{{ $tour->description_simplified }}</textarea>
                                                        @error('description_simplified')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="itinerary" class="form-label col-form-label">Itinerary<span> *</span></label>
                                                        <textarea id="itinerary" name="itinerary" class="textarea_editor backend-form-control @error('itinerary') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert itinerary" value="{{ $tour->itinerary }}" required>{{ $tour->itinerary }}</textarea>
                                                        @error('itinerary')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="itinerary_traditional" class="form-label col-form-label">Itinerary Traditional<span> *</span></label>
                                                        <textarea id="itinerary_traditional" name="itinerary_traditional" class="textarea_editor backend-form-control @error('itinerary_traditional') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert itinerary in Chinese traditional" value="{{ $tour->itinerary_traditional }}" required>{{ $tour->itinerary_traditional }}</textarea>
                                                        @error('itinerary_traditional')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="itinerary_simplified" class="form-label col-form-label">Itinerary Simplified<span> *</span></label>
                                                        <textarea id="itinerary_simplified" name="itinerary_simplified" class="textarea_editor backend-form-control @error('itinerary_simplified') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert itinerary in Chinese simplified" value="{{ $tour->itinerary_simplified }}" required>{{ $tour->itinerary_simplified }}</textarea>
                                                        @error('itinerary_simplified')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="include" class="form-label col-form-label">Include<span> *</span></label>
                                                        <textarea id="include" name="include" class="textarea_editor backend-form-control @error('include') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert include" value="{{ $tour->include }}" required>{{ $tour->include }}</textarea>
                                                        @error('include')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="include_traditional" class="form-label col-form-label">Include Traditional<span> *</span></label>
                                                        <textarea id="include_traditional" name="include_traditional" class="textarea_editor backend-form-control @error('include_traditional') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert include in Chinese traditional" value="{{ $tour->include_traditional }}" required>{{ $tour->include_traditional }}</textarea>
                                                        @error('include_traditional')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="include_simplified" class="form-label col-form-label">Include Simplified<span> *</span></label>
                                                        <textarea id="include_simplified" name="include_simplified" class="textarea_editor backend-form-control @error('include_simplified') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert include in Chinese simplified" value="{{ $tour->include_simplified }}" required>{{ $tour->include_simplified }}</textarea>
                                                        @error('include_simplified')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="package_highlights" class="form-label col-form-label">Package Highlights</label>
                                                        <textarea id="package_highlights" name="package_highlights" class="textarea_editor backend-form-control @error('package_highlights') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert package highlights">{{ old('package_highlights', $tour->package_highlights) }}</textarea>
                                                        @error('package_highlights')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="package_highlights_traditional" class="form-label col-form-label">Package Highlights Traditional</label>
                                                        <textarea id="package_highlights_traditional" name="package_highlights_traditional" class="textarea_editor backend-form-control @error('package_highlights_traditional') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert package highlights in Chinese traditional">{{ old('package_highlights_traditional', $tour->package_highlights_traditional) }}</textarea>
                                                        @error('package_highlights_traditional')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="package_highlights_simplified" class="form-label col-form-label">Package Highlights Simplified</label>
                                                        <textarea id="package_highlights_simplified" name="package_highlights_simplified" class="textarea_editor backend-form-control @error('package_highlights_simplified') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert package highlights in Chinese simplified">{{ old('package_highlights_simplified', $tour->package_highlights_simplified) }}</textarea>
                                                        @error('package_highlights_simplified')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="exclude" class="form-label col-form-label">Exclude</label>
                                                        <textarea id="exclude" name="exclude" class="textarea_editor backend-form-control @error('exclude') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert exclude" value="{{ $tour->exclude }}">{{ $tour->exclude }}</textarea>
                                                        @error('exclude')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="exclude_traditional" class="form-label col-form-label">Exclude Traditional</label>
                                                        <textarea id="exclude_traditional" name="exclude_traditional" class="textarea_editor backend-form-control @error('exclude_traditional') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert exclude in Chinese traditional" value="{{ $tour->exclude_traditional }}">{{ $tour->exclude_traditional }}</textarea>
                                                        @error('exclude_traditional')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="exclude_simplified" class="form-label col-form-label">Exclude Simplified</label>
                                                        <textarea id="exclude_simplified" name="exclude_simplified" class="textarea_editor backend-form-control @error('exclude_simplified') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert exclude in Chinese simplified" value="{{ $tour->exclude_simplified }}">{{ $tour->exclude_simplified }}</textarea>
                                                        @error('exclude_simplified')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="additional_info" class="form-label col-form-label">Additional Information</label>
                                                        <textarea id="additional_info" name="additional_info" class="textarea_editor backend-form-control @error('additional_info') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert additional info" value="{{ $tour->additional_info }}" required>{{ $tour->additional_info }}</textarea>
                                                        @error('additional_info')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="additional_info_traditional" class="form-label col-form-label">Additional Information Traditional</label>
                                                        <textarea id="additional_info_traditional" name="additional_info_traditional" class="textarea_editor backend-form-control @error('additional_info_traditional') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert additional info in Chinese traditional" value="{{ $tour->additional_info_traditional }}" required>{{ $tour->additional_info_traditional }}</textarea>
                                                        @error('additional_info_traditional')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="additional_info_simplified" class="form-label col-form-label">Additional Information Simplified</label>
                                                        <textarea id="additional_info_simplified" name="additional_info_simplified" class="textarea_editor backend-form-control @error('additional_info_simplified') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert additional info in Chinese simplified" value="{{ $tour->additional_info_simplified }}" required>{{ $tour->additional_info_simplified }}</textarea>
                                                        @error('additional_info_simplified')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="cancellation_policy" class="form-label col-form-label">Cancellation Policy<span> *</span></label>
                                                        <textarea id="cancellation_policy" name="cancellation_policy" class="textarea_editor backend-form-control @error('cancellation_policy') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert cancellation policy" required>{{ old('cancellation_policy', $tour->cancellation_policy) }}</textarea>
                                                        @error('cancellation_policy')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="cancellation_policy_traditional" class="form-label col-form-label">Cancellation Policy Traditional<span> *</span></label>
                                                        <textarea id="cancellation_policy_traditional" name="cancellation_policy_traditional" class="textarea_editor backend-form-control @error('cancellation_policy_traditional') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert cancellation policy in Chinese traditional" required>{{ old('cancellation_policy_traditional', $tour->cancellation_policy_traditional) }}</textarea>
                                                        @error('cancellation_policy_traditional')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="backend-form-field">
                                                        <label for="cancellation_policy_simplified" class="form-label col-form-label">Cancellation Policy Simplified<span> *</span></label>
                                                        <textarea id="cancellation_policy_simplified" name="cancellation_policy_simplified" class="textarea_editor backend-form-control @error('cancellation_policy_simplified') is-invalid @enderror" data-backend-richtext="true" placeholder="Insert cancellation policy in Chinese simplified" required>{{ old('cancellation_policy_simplified', $tour->cancellation_policy_simplified) }}</textarea>
                                                        @error('cancellation_policy_simplified')
                                                            <div class="backend-form-error">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @include('backend.operations.tours.partials.tour-location-repeater', ['tour' => $tour])
                                        <input class="backend-form-control" id="initial_state" name="initial_state" value="{{ $tour->status }}" type="hidden">
                                    </div>
                                </form>
                                <div class="backend-form-actions">
                                    <button type="submit" form="updateTour{{ $tour->id }}" class="backend-button backend-button-primary">
                                        <i class="fa fa-check" aria-hidden="true"></i>
                                        Update
                                    </button>
                                    <a href="{{ route('admin.tours.show', $tour['id']) }}" class="backend-button backend-button-secondary">
                                        <i class="fa fa-times" aria-hidden="true"></i>
                                        Cancel
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 desktop">
                            <div class="row">
                               @include('admin.usd-rate')
                            </div>
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </main>
    @endcan
@endsection
