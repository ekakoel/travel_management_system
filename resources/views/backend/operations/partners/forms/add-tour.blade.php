@section('title', __('messages.Partner'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Tour Package
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                <li class="breadcrumb-item"><a href="/partners">Partners</a></li>
                                <li class="breadcrumb-item"><a href="/detail-partner-{{ $partners->id }}">{{ $partners->name }}</a></li>
                                <li class="breadcrumb-item active">Add Tour Package</li>
                            </ol>
                        </nav>
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
                                <div class="card-box-title">
                                    <div class="title"><i class="fa fa-plus"></i> Add Tour to {{ $partners->name }}</div>
                                </div>
                                <form id="add-tour" action="/fpartner-add-tour" method="post" enctype="multipart/form-data" id="my-awesome-dropzone">
                                    @csrf
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="card-subtitle m-b-8">Cover Image</div>
                                                    <div class="dropzone text-center pd-20 m-b-18">
                                                        <div class="cover-preview-div">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="form-group">
                                                <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert tour package name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="location">Location</label>
                                                <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" placeholder="Insert tour location" value="{{ old('location') }}" required>
                                                @error('location')location
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="type">Type<span> *</span></label>
                                                <select id="type" name="type" class="custom-select col-12 @error('type') is-invalid @enderror" required>
                                                    <option selected value="">Select Type</option>
                                                    <option value="Private">Private</option>
                                                    <option value="Group">Group</option>
                                                    <option value="Shared">Shared</option>
                                                </select>
                                                @error('type')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="duration">Duration<span> *</span></label>
                                                <select id="duration" name="duration" value="{{ old('duration') }}" class="custom-select col-12 @error('duration') is-invalid @enderror" required>
                                                    <option selected value="">Select duration</option>
                                                    <option value="1D">1D</option>
                                                    <option value="2D/1N">2D/1N</option>
                                                    <option value="3D/2N">3D/2N</option>
                                                    <option value="4D/3N">4D/3N</option>
                                                    <option value="5D/4N">5D/4N</option>
                                                    <option value="6D/5N">6D/5N</option>
                                                    <option value="7D/6N">7D/6N</option>
                                                </select>
                                                @error('duration')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        {{-- <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="qty">Capacity</label>
                                                <input type="number" id="qty" name="qty" value="{{ old('qty') }}" class="form-control @error('qty') is-invalid @enderror" placeholder="Insert capacity" required>
                                                @error('qty')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div> --}}
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="description">Description</label>
                                                <textarea id="description" name="description" class="textarea_editor form-control border-radius-0" placeholder="Insert description" value="{{ old('description') }}" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="destinations">Destinations</label>
                                                <textarea id="destinations" name="destinations" class="textarea_editor form-control border-radius-0" placeholder="Insert destinations" value="{{ old('destinations') }}" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="itinerary">Itinerary</label>
                                                <textarea id="itinerary" name="itinerary" class="textarea_editor form-control border-radius-0" placeholder="Insert itinerary" value="{{ old('itinerary') }}" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="include">Include</label>
                                                <textarea id="include" name="include" class="textarea_editor form-control border-radius-0" placeholder="Insert include" value="{{ old('include') }}" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="cancellation_policy">Cancellation Policy</label>
                                                <textarea id="cancellation_policy" name="cancellation_policy" class="textarea_editor form-control border-radius-0" placeholder="Insert additional information" value="{{ old('cancellation_policy') }}"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="additional_info">Additional Information</label>
                                                <textarea id="additional_info" name="additional_info" class="textarea_editor form-control border-radius-0" placeholder="Insert additional information" value="{{ old('additional_info') }}"></textarea>
                                            </div>
                                        </div>
                                        <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                        <input id="partners_id" name="partners_id" value="{{ $partners->id }}" type="hidden">
                                        <input id="page" name="page" value="add-tour" type="hidden">
                                        <input id="initial_state" name="initial_state" value="" type="hidden">
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="add-tour" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add Tour</button>
                                    <a href="/detail-partner-{{ $partners->id }}">
                                        <button type="button"class="btn btn-danger"><i class="icon-copy fa fa-remove" aria-hidden="true"></i> Cancel</button>
                                    </a>
                                </div>
                            </div>
                            @if (count($attentions)>0)
                                <div class="col-md-4">
                                    <div class="card-box mb-30">
                                        <div class="banner-right">
                                            <div class="title">Attention</div>
                                            <ul class="attention">
                                                @foreach ($attentions as $attention)
                                                    <li><p><b>"{{ $attention->name }}"</b> {{ $attention->attention }}</p></li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
@endsection
