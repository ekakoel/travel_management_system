@section('title', __('messages.Tour'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i>Edit Tour Packages
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                <li class="breadcrumb-item"><a href="/tours-admin">Tours Package</a></li>
                                <li class="breadcrumb-item"><a href="/detail-tour-{{ $tour->id }}">Tour Detail</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ "Edit Tour ". $tour->name }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="row">
                        {{-- ATTENTIONS --}}
                        <div class="col-md-4 mobile">
                            <div class="row">
                                @include('admin.usd-rate')
                                @include('layouts.attentions')
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-briefcase"></i>{{ $tour->name }}</div>
                                </div>
                                <form id="updateTour{{ $tour->id }}" action="{{ route('func.tour.update',$tour->id) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                                    <div class="row">
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
                                            <div class="form-group">
                                                <label for="cover" class="form-label col-form-label">Cover Image </label>
                                                <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="cover" class="form-label col-form-label">Status </label>
                                                <select id="status" name="status" class="custom-select col-12 @error('status') is-invalid @enderror" required>
                                                    <option {{ $tour->status == "Active" ?"selected":""; }} value="Active">Active</option>
                                                    <option {{ $tour->status == "Draft" ?"selected":""; }} value="Draft">Draft</option>
                                                </select>
                                                @error('status')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-8">
                                            <div class="form-group">
                                                <label for="name" class="form-label">Tour Name</label>
                                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert tour package name" value="{{ $tour->name }}" required>
                                                @error('name')
                                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-8">
                                            <div class="form-group">
                                                <label for="name_traditional" class="form-label">Tour Name Traditional</label>
                                                <input type="text" id="name_traditional" name="name_traditional" class="form-control @error('name_traditional') is-invalid @enderror" placeholder="Insert tour package name in traditional" value="{{ $tour->name_traditional }}" required>
                                                @error('name_traditional')
                                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-8">
                                            <div class="form-group">
                                                <label for="name_simplified" class="form-label">Tour Name Simplified</label>
                                                <input type="text" id="name_simplified" name="name_simplified" class="form-control @error('name_simplified') is-invalid @enderror" placeholder="Insert tour package name in simplified" value="{{ $tour->name_simplified }}" required>
                                                @error('name_simplified')
                                                    <div class="alert-form alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-4 col-md-4">
                                                    <div class="form-group">
                                                        <label for="type" class="form-label">Type <span> *</span></label>
                                                        <select id="type" name="type" class="custom-select col-12 @error('type') is-invalid @enderror" required>
                                                            @foreach ($types as $type)
                                                                <option {{ $tour->type_id == $type->id?"selected":"" }} selected value="{{ $type->id }}">{{ $type->type }}</option>
                                                            @endforeach
                                                        </select>
                                                        @error('type')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4 col-md-4">
                                                    <div class="form-group">
                                                        <label for="duration_days" class="form-label">Duration Days <span> *</span></label>
                                                        <select id="duration_days" name="duration_days" class="custom-select col-12 @error('duration_days') is-invalid @enderror" required>
                                                            <option {{ $tour->duration_days == 1?"selected":"" }} value="1">1D</option>
                                                            <option {{ $tour->duration_days == 2?"selected":"" }} value="2">2D</option>
                                                            <option {{ $tour->duration_days == 3?"selected":"" }} value="3">3D</option>
                                                            <option {{ $tour->duration_days == 4?"selected":"" }} value="4">4D</option>
                                                            <option {{ $tour->duration_days == 5?"selected":"" }} value="5">5D</option>
                                                            <option {{ $tour->duration_days == 6?"selected":"" }} value="6">6D</option>
                                                            <option {{ $tour->duration_days == 7?"selected":"" }} value="7">7D</option>
                                                        </select>
                                                        @error('duration_days')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4 col-md-4">
                                                    <div class="form-group">
                                                        <label for="duration_nights" class="form-label">Duration Nights <span> *</span></label>
                                                        <select id="duration_nights" name="duration_nights" class="custom-select col-12 @error('duration_nights') is-invalid @enderror" required>
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
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="short_description" class="form-label col-form-label">Short Description<span> *</span></label>
                                                        <textarea id="short_description" name="short_description" class="textarea_editor form-control @error('short_description') is-invalid @enderror" placeholder="Insert short description" value="{{ $tour->short_description }}" required>{{ $tour->short_description }}</textarea>
                                                        @error('short_description')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="short_description_traditional" class="form-label col-form-label">Short Description Traditional<span> *</span></label>
                                                        <textarea id="short_description_traditional" name="short_description_traditional" class="textarea_editor form-control @error('short_description_traditional') is-invalid @enderror" placeholder="Insert short description in Chinese traditional" value="{{ $tour->short_description_traditional }}" required>{{ $tour->short_description_traditional }}</textarea>
                                                        @error('short_description_traditional')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="short_description_simplified" class="form-label col-form-label">Short Description Simplified<span> *</span></label>
                                                        <textarea id="short_description_simplified" name="short_description_simplified" class="textarea_editor form-control @error('short_description_simplified') is-invalid @enderror" placeholder="Insert short description in Chinese simplified" value="{{ $tour->short_description_simplified }}" required>{{ $tour->short_description_simplified }}</textarea>
                                                        @error('short_description_simplified')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="description" class="form-label col-form-label">Description</label>
                                                        <textarea id="description" name="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Insert description" value="{{ $tour->description }}">{{ $tour->description }}</textarea>
                                                        @error('description')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="description_traditional" class="form-label col-form-label">Description Traditional</label>
                                                        <textarea id="description_traditional" name="description_traditional" class="textarea_editor form-control @error('description_traditional') is-invalid @enderror" placeholder="Insert description in Chinese traditional" value="{{ $tour->description_traditional }}">{{ $tour->description_traditional }}</textarea>
                                                        @error('description_traditional')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="description_simplified" class="form-label col-form-label">Description Simplified</label>
                                                        <textarea id="description_simplified" name="description_simplified" class="textarea_editor form-control @error('description_simplified') is-invalid @enderror" placeholder="Insert description in Chinese simplified" value="{{ $tour->description_simplified }}">{{ $tour->description_simplified }}</textarea>
                                                        @error('description_simplified')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="itinerary" class="form-label col-form-label">Itinerary<span> *</span></label>
                                                        <textarea id="itinerary" name="itinerary" class="textarea_editor form-control @error('itinerary') is-invalid @enderror" placeholder="Insert itinerary" value="{{ $tour->itinerary }}" required>{{ $tour->itinerary }}</textarea>
                                                        @error('itinerary')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="itinerary_traditional" class="form-label col-form-label">Itinerary Traditional<span> *</span></label>
                                                        <textarea id="itinerary_traditional" name="itinerary_traditional" class="textarea_editor form-control @error('itinerary_traditional') is-invalid @enderror" placeholder="Insert itinerary in Chinese traditional" value="{{ $tour->itinerary_traditional }}" required>{{ $tour->itinerary_traditional }}</textarea>
                                                        @error('itinerary_traditional')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="itinerary_simplified" class="form-label col-form-label">Itinerary Simplified<span> *</span></label>
                                                        <textarea id="itinerary_simplified" name="itinerary_simplified" class="textarea_editor form-control @error('itinerary_simplified') is-invalid @enderror" placeholder="Insert itinerary in Chinese simplified" value="{{ $tour->itinerary_simplified }}" required>{{ $tour->itinerary_simplified }}</textarea>
                                                        @error('itinerary_simplified')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="include" class="form-label col-form-label">Include<span> *</span></label>
                                                        <textarea id="include" name="include" class="textarea_editor form-control @error('include') is-invalid @enderror" placeholder="Insert include" value="{{ $tour->include }}" required>{{ $tour->include }}</textarea>
                                                        @error('include')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="include_traditional" class="form-label col-form-label">Include Traditional<span> *</span></label>
                                                        <textarea id="include_traditional" name="include_traditional" class="textarea_editor form-control @error('include_traditional') is-invalid @enderror" placeholder="Insert include in Chinese traditional" value="{{ $tour->include_traditional }}" required>{{ $tour->include_traditional }}</textarea>
                                                        @error('include_traditional')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="include_simplified" class="form-label col-form-label">Include Simplified<span> *</span></label>
                                                        <textarea id="include_simplified" name="include_simplified" class="textarea_editor form-control @error('include_simplified') is-invalid @enderror" placeholder="Insert include in Chinese simplified" value="{{ $tour->include_simplified }}" required>{{ $tour->include_simplified }}</textarea>
                                                        @error('include_simplified')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="exclude" class="form-label col-form-label">Exclude</label>
                                                        <textarea id="exclude" name="exclude" class="textarea_editor form-control @error('exclude') is-invalid @enderror" placeholder="Insert exclude" value="{{ $tour->exclude }}">{{ $tour->exclude }}</textarea>
                                                        @error('exclude')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="exclude_traditional" class="form-label col-form-label">Exclude Traditional</label>
                                                        <textarea id="exclude_traditional" name="exclude_traditional" class="textarea_editor form-control @error('exclude_traditional') is-invalid @enderror" placeholder="Insert exclude in Chinese traditional" value="{{ $tour->exclude_traditional }}">{{ $tour->exclude_traditional }}</textarea>
                                                        @error('exclude_traditional')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="exclude_simplified" class="form-label col-form-label">Exclude Simplified</label>
                                                        <textarea id="exclude_simplified" name="exclude_simplified" class="textarea_editor form-control @error('exclude_simplified') is-invalid @enderror" placeholder="Insert exclude in Chinese simplified" value="{{ $tour->exclude_simplified }}">{{ $tour->exclude_simplified }}</textarea>
                                                        @error('exclude_simplified')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="additional_info" class="form-label col-form-label">Additional Information</label>
                                                        <textarea id="additional_info" name="additional_info" class="textarea_editor form-control @error('additional_info') is-invalid @enderror" placeholder="Insert additional info" value="{{ $tour->additional_info }}" required>{{ $tour->additional_info }}</textarea>
                                                        @error('additional_info')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="additional_info_traditional" class="form-label col-form-label">Additional Information Traditional</label>
                                                        <textarea id="additional_info_traditional" name="additional_info_traditional" class="textarea_editor form-control @error('additional_info_traditional') is-invalid @enderror" placeholder="Insert additional info in Chinese traditional" value="{{ $tour->additional_info_traditional }}" required>{{ $tour->additional_info_traditional }}</textarea>
                                                        @error('additional_info_traditional')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="additional_info_simplified" class="form-label col-form-label">Additional Information Simplified</label>
                                                        <textarea id="additional_info_simplified" name="additional_info_simplified" class="textarea_editor form-control @error('additional_info_simplified') is-invalid @enderror" placeholder="Insert additional info in Chinese simplified" value="{{ $tour->additional_info_simplified }}" required>{{ $tour->additional_info_simplified }}</textarea>
                                                        @error('additional_info_simplified')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="cancellation_policy" class="form-label col-form-label">Cancellation Policy</label>
                                                        <textarea id="cancellation_policy" name="cancellation_policy" class="textarea_editor form-control @error('cancellation_policy') is-invalid @enderror" placeholder="Insert cancellation policy" value="{{ $tour->cancellation_policy }}">{{ $tour->cancellation_policy }}</textarea>
                                                        @error('cancellation_policy')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="cancellation_policy_traditional" class="form-label col-form-label">Cancellation Policy Traditional</label>
                                                        <textarea id="cancellation_policy_traditional" name="cancellation_policy_traditional" class="textarea_editor form-control @error('cancellation_policy_traditional') is-invalid @enderror" placeholder="Insert cancellation policy in Chinese traditional" value="{{ $tour->cancellation_policy_traditional }}">{{ $tour->cancellation_policy_traditional }}</textarea>
                                                        @error('cancellation_policy_traditional')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="cancellation_policy_simplified" class="form-label col-form-label">Cancellation Policy Simplified</label>
                                                        <textarea id="cancellation_policy_simplified" name="cancellation_policy_simplified" class="textarea_editor form-control @error('cancellation_policy_simplified') is-invalid @enderror" placeholder="Insert cancellation policy in Chinese simplified" value="{{ $tour->cancellation_policy_simplified }}">{{ $tour->cancellation_policy_simplified }}</textarea>
                                                        @error('cancellation_policy_simplified')
                                                            <div class="alert-form alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input id="initial_state" name="initial_state" value="{{ $tour->status }}" type="hidden">
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="updateTour{{ $tour->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                    <a href="detail-tour-{{ $tour['id'] }}">
                                        <button type="button"class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- ATTENTIONS --}}
                        <div class="col-md-4 desktop">
                            <div class="row">
                               @include('admin.usd-rate')
                                @include('layouts.attentions')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    @endcan
@endsection