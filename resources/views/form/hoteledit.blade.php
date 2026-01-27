@section('title', __('messages.Hotels'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        @can('isAdmin')
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Hotel Edit</div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                <li class="breadcrumb-item"><a href="/hotels-admin">Hotels</a></li>
                                <li class="breadcrumb-item active">Edit Hotel {{ $hotels->name }}</li>
                            </ol>
                        </nav>
                    </div>
                    @if (count($errors) > 0)
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif
                    @if (\Session::has('success'))
                        <div class="alert alert-success">
                            <ul>
                                <li>{!! \Session::get('success') !!}</li>
                            </ul>
                        </div>
                    @endif
                    <div class="row">
                        {{-- ATTENTIONS --}}
                        <div class="col-md-4 mobile">
                            <div class="row">
                                @include('admin.usd-rate')
                                @include('layouts.attentions')
                            </div>
                        </div>
                        {{-- HOTEL DETAIL --}}
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">{{ $hotels->name }}</div>
                                </div>
                                <form id="update-hotel" action="{{ route("func.hotel.edit",$hotels->id) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="preview-cover">
                                                        <img src="{{ asset('storage/hotels/hotels-cover/'. $hotels->cover)  }}" alt="{{ $hotels->name }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="dropzone">
                                                        <div class="cover-preview-div">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="cover" class="form-label">Cover Image </label>
                                                        <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover">
                                                        @error('cover')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6 col-md-6">
                                                    <div class="form-group">
                                                        <label for="cover" class="form-label">Status</label>
                                                        <select id="status" name="status" class="form-control custom-select @error('status') is-invalid @enderror" required>
                                                            <option selected="{{ $hotels->status }}">{{ $hotels->status }}</option>
                                                            <option value="Active">Active</option>
                                                            <option value="Draft">Draft</option>
                                                            <option value="Archived">Archived</option>
                                                        </select>
                                                        @error('status')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="contact_person" class="form-label">Contact Person </label>
                                                <input type="text" id="contact_person" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" placeholder="Insert contact person" value="{{ $hotels->contact_person }}" required>
                                                @error('contact_person')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="phone" class="form-label">Phone Number </label>
                                                <input type="number" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Insert contact person phone" value="{{ $hotels->phone }}" required>
                                                @error('phone')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="name" class="form-label">Hotel Name </label>
                                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert hotel name" value="{{ $hotels->name }}" required>
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3 col-md-3">
                                            <div class="form-group">
                                                <label for="min_stay" class="form-label">Minimum Stay </label>
                                                <input type="number" min="1" max="7" id="min_stay" name="min_stay" class="form-control @error('min_stay') is-invalid @enderror" placeholder="Minimum stay" value="{{ $hotels->min_stay }}" required>
                                                @error('min_stay')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3 col-md-3">
                                            <div class="form-group">
                                                <label for="max_stay" class="form-label">Maximum Stay </label>
                                                <input type="number" min="8"  id="max_stay" name="max_stay" class="form-control @error('max_stay') is-invalid @enderror" placeholder="Maximum stay" value="{{ $hotels->max_stay }}" required>
                                                @error('max_stay')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="address" class="form-label">Address </label>
                                                <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Insert address" value="{{ $hotels->address }}" required>
                                                @error('address')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="region" class="form-label">Region </label>
                                                <input type="text" id="region" name="region" class="form-control @error('region') is-invalid @enderror" placeholder="Insert region" value="{{ $hotels->region }}" required>
                                                @error('region')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="web" class="form-label">Website </label>
                                                <input type="text" id="web" name="web" class="form-control @error('web') is-invalid @enderror" placeholder="Ex: www.example.com" value="{{ $hotels->web }}" required>
                                                @error('web')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="map" class="form-label">Map Location </label>
                                                <input type="text" id="map" name="map" class="form-control @error('map') is-invalid @enderror" placeholder="Google Map link" value="{{ $hotels->map }}" required>
                                                @error('map')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="airport_duration" class="form-label">Airport Duration (Hours)</label>
                                                <input type="number" min="1" id="airport_duration" name="airport_duration" class="form-control @error('airport_duration') is-invalid @enderror" placeholder="Duration to airport" value="{{ $hotels->airport_duration }}" required>
                                                @error('airport_duration')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="airport_distance" class="form-label">Airport Distance (Km)</label>
                                                <input type="number" min="1" id="airport_distance" name="airport_distance" class="form-control @error('airport_distance') is-invalid @enderror" placeholder="Distance to airport" value="{{ $hotels->airport_distance }}" required>
                                                @error('airport_distance')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row my-18">
                                                <div class="col-md-12">
                                                    <div class="tab-inner-title">Description</div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group">
                                                        <label for="description" class="form-label">English </label>
                                                        <textarea id="description" name="description" class="textarea_editor form-control border-radius-0" placeholder="Insert description">{!! $hotels->description !!}</textarea>
                                                        @error('description')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group">
                                                        <label for="description_traditional" class="form-label">Chinese Traditional </label>
                                                        <textarea id="description_traditional" name="description_traditional" class="textarea_editor form-control border-radius-0" placeholder="Insert description_traditional">{!! $hotels->description_traditional !!}</textarea>
                                                        @error('description_traditional')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group">
                                                        <label for="description_simplified" class="form-label">Chinese Simplified </label>
                                                        <textarea id="description_simplified" name="description_simplified" class="textarea_editor form-control border-radius-0" placeholder="Insert description_simplified">{!! $hotels->description_simplified !!}</textarea>
                                                        @error('description_simplified')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row my-18">
                                                <div class="col-md-12">
                                                    <div class="tab-inner-title">Facility</div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group">
                                                        <label for="facility" class="form-label">English </label>
                                                        <textarea id="facility" name="facility" class="textarea_editor form-control border-radius-0" placeholder="Insert facility">{!! $hotels->facility !!}</textarea>
                                                        @error('facility')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group">
                                                        <label for="facility_traditional" class="form-label">Chinese Traditional </label>
                                                        <textarea id="facility_traditional" name="facility_traditional" class="textarea_editor form-control border-radius-0" placeholder="Insert facility_traditional">{!! $hotels->facility_traditional !!}</textarea>
                                                        @error('facility_traditional')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group">
                                                        <label for="facility_simplified" class="form-label">Chinese Simplified </label>
                                                        <textarea id="facility_simplified" name="facility_simplified" class="textarea_editor form-control border-radius-0" placeholder="Insert facility_simplified">{!! $hotels->facility_simplified !!}</textarea>
                                                        @error('facility_simplified')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row my-18">
                                                <div class="col-md-12">
                                                    <div class="tab-inner-title">Cancellation Policy</div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group">
                                                        <label for="cancellation_policy" class="form-label">English</label>
                                                        <textarea id="cancellation_policy" name="cancellation_policy" class="textarea_editor form-control border-radius-0" placeholder="Insert cancellation policy">{!! $hotels->cancellation_policy !!}</textarea>
                                                        @error('cancellation_policy')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group">
                                                        <label for="cancellation_policy_traditional" class="form-label">Chinese Traditional</label>
                                                        <textarea id="cancellation_policy_traditional" name="cancellation_policy_traditional" class="textarea_editor form-control border-radius-0" placeholder="Insert cancellation policy">{!! $hotels->cancellation_policy_traditional !!}</textarea>
                                                        @error('cancellation_policy_traditional')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group">
                                                        <label for="cancellation_policy_simplified" class="form-label">Chinese Simplified</label>
                                                        <textarea id="cancellation_policy_simplified" name="cancellation_policy_simplified" class="textarea_editor form-control border-radius-0" placeholder="Insert cancellation policy">{!! $hotels->cancellation_policy_simplified !!}</textarea>
                                                        @error('cancellation_policy_simplified')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row my-18">
                                                <div class="col-md-12">
                                                    <div class="tab-inner-title">Additional Information</div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group">
                                                        <label for="additional_info" class="form-label">English</label>
                                                        <textarea id="additional_info" name="additional_info" class="textarea_editor form-control border-radius-0" placeholder="Insert additional information">{!! $hotels->additional_info !!}</textarea>
                                                        @error('additional_info')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group">
                                                        <label for="additional_info_traditional" class="form-label">Chinese Traditional</label>
                                                        <textarea id="additional_info_traditional" name="additional_info_traditional" class="textarea_editor form-control border-radius-0" placeholder="Insert additional information">{!! $hotels->additional_info_traditional !!}</textarea>
                                                        @error('additional_info_traditional')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-12 col-md-12">
                                                    <div class="form-group">
                                                        <label for="additional_info_simplified" class="form-label">Chinese Simplified</label>
                                                        <textarea id="additional_info_simplified" name="additional_info_simplified" class="textarea_editor form-control border-radius-0" placeholder="Insert additional information">{!! $hotels->additional_info_simplified !!}</textarea>
                                                        @error('additional_info_simplified')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                        <input id="page" name="page" value="edit-hotel" type="hidden">
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="update-hotel" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                    <a href="/detail-hotel-{{ $hotels->id }}">
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
                        
                    @include('layouts.footer')
                </div>
            </div>
        @endcan
    @endsection
    
