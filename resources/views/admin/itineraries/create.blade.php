@extends('layouts.head')
@section('title', __('messages.Create Itinerary'))
@section('content')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <i class="icon-copy dw dw-map-6" aria-hidden="true"></i> Itineraries
                                </div>
                                 <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">Data</li>
                                        <li class="breadcrumb-item"><a href="{{ route('itineraries.index') }}">Itineraries</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Create Itinerary</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="info-action">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (\Session::has('success'))
                            <div class="alert alert-success">
                                <ul>
                                    <li>{!! \Session::get('success') !!}</li>
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card-box">
                                <form id="creteItinerary" action="{{ route('itineraries.store') }}" method="POST" class="space-y-8">
                                    @csrf
                                    <div class="card-box-title">
                                        <div class="title">Create New Itineraries</div>
                                    </div>
                                        <!-- Form -->
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    <label for="title">Title</label>
                                                    <input type="text" name="title" class="form-control m-0 @error('title') is-invalid @enderror" placeholder="Insert Itinerary Title" value="{{ old('title') }}" required>
                                                    @error('title')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="code">Code</label>
                                                    <input type="text" name="code" class="form-control m-0 @error('code') is-invalid @enderror" placeholder="Insert Itinerary Code" value="{{ old('code') }}" required>
                                                    @error('code')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    <label for="title_traditional">Title (Traditional)</label>
                                                    <input type="text" name="title_traditional" class="form-control m-0 @error('title_traditional') is-invalid @enderror" placeholder="Insert Itinerary Title in Chinese Traditional" value="{{ old('title_traditional') }}">
                                                    @error('title_traditional')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-8">
                                                <div class="form-group">
                                                    <label for="title_simplified">Title (Simplified)</label>
                                                    <input type="text" name="title_simplified" class="form-control m-0 @error('title_simplified') is-invalid @enderror" placeholder="Insert Itinerary Title in Chinese Simplified" value="{{ old('title_simplified') }}">
                                                    @error('title_simplified')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="itinerary">Itinerary <span>*</span></label>
                                                    <textarea id="itinerary" name="itinerary" placeholder="@lang('messages.Optional')" class="textarea_editor form-control border-radius-0" value="{{ old('itinerary') }}"></textarea>
                                                    @error('itinerary')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="itinerary_traditional">Itinerary (Traditional)</label>
                                                    <textarea id="itinerary_traditional" name="itinerary_traditional" placeholder="@lang('messages.Optional')" class="textarea_editor form-control border-radius-0" value="{{ old('itinerary_traditional') }}"></textarea>
                                                    @error('itinerary_traditional')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label for="itinerary_simplified">Itinerary (Simplified)</label>
                                                    <textarea id="itinerary_simplified" name="itinerary_simplified" placeholder="@lang('messages.Optional')" class="textarea_editor form-control border-radius-0" value="{{ old('itinerary_simplified') }}"></textarea>
                                                    @error('itinerary_simplified')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    
                                    <div class="card-box-footer">
                                        <!-- Actions -->
                                        <div class="flex justify-end gap-3">
                                            <button type="reset"
                                                class="btn btn-secondary">
                                                Reset
                                            </button>

                                            <button type="submit" form="creteItinerary"
                                                class="btn btn-primary">
                                                Save Itinerary
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection

            