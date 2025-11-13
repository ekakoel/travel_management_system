@section('title', __('messages.Villa Room'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Room Villa</div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('villas-admin.index') }}">Villas</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.villa.show',$villa->id) }}">{{ $villa->name }}</a></li>
                                <li class="breadcrumb-item active">Add New Room</li>
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
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">Detail Rooms</div>
                                </div>
                                <form id="addVillaRoom" action="{{ route('func.add-villa-room') }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="cover" class="form-label">Cover Image</label>
                                                        <div class="dropzone">
                                                            <div class="cover-preview-div">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="cover" class="form-label">Cover Image <span>*</span></label>
                                                <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name" class="form-label">Name </label>
                                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert room name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="guest_adult" class="form-label">Capacity Adult </label>
                                                <input type="number" min="1" id="guest_adult" name="guest_adult" class="form-control @error('guest_adult') is-invalid @enderror" placeholder="Insert guest adult" value="{{ old('guests_adult') }}" required>
                                                @error('guest_adult')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="guest_child" class="form-label">Capacity Child </label>
                                                <input type="number" min="0" id="guest_child" name="guest_child" class="form-control @error('guest_child') is-invalid @enderror" placeholder="Insert guest child" value="{{ old('guests_child') }}" required>
                                                @error('guest_child')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="room_type" class="form-label">Room Type <span>*</span></label>
                                                <select id="room_type" name="room_type" class="form-control custom-select @error('room_type') is-invalid @enderror" required>
                                                    <option selected>Select type</option>
                                                    <option value="Master Suite">Master Suite</option>
                                                    <option value="Deluxe Room">Deluxe Room</option>
                                                    <option value="Twin Room">Twin Room</option>
                                                    <option value="Family Room">Family Room</option>
                                                    <option value="Honeymoon Suite">Honeymoon Suite</option>
                                                </select>
                                                @error('room_type')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="view" class="form-label">Room View</label>
                                                <input type="text" name="view" id="view" class="custom-file-input @error('view') is-invalid @enderror" placeholder="Insert room view" value="{{ old('view') }}" required>
                                                @error('view')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="bed_type" class="form-label">Bed Type <span>*</span></label>
                                                <select id="bed_type" name="bed_type" class="form-control custom-select @error('bed_type') is-invalid @enderror" required>
                                                    <option selected>Select bed type</option>
                                                    <option value="King">King</option>
                                                    <option value="Queen">Queen</option>
                                                    <option value="Twin">Twin</option>
                                                    <option value="Single">Single</option>
                                                    <option value="Double">Double</option>
                                                </select>
                                                @error('bed_type')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="size" class="form-label">Room Size (m²)</label>
                                                <input type="number" min="0" id="size" name="size" class="form-control @error('size') is-invalid @enderror" placeholder="Insert size" value="{{ old('size') }}" required>
                                                @error('size')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="tab-inner-title">Amenities</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="amenities" class="form-label">English</label>
                                                        <textarea id="amenities" name="amenities" class="textarea_editor form-control" placeholder="Insert amenities">{{ old('amenities') }}</textarea>
                                                    </div>
                                                    @error('amenities')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="amenities_traditional" class="form-label">Traditional</label>
                                                        <textarea id="amenities_traditional" name="amenities_traditional" class="textarea_editor form-control" placeholder="Insert Amenities traditional">{{ old('amenities_traditional') }}</textarea>
                                                    </div>
                                                    @error('amenities_traditional')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="amenities_simplified" class="form-label">Simplified</label>
                                                        <textarea id="amenities_simplified" name="amenities_simplified" class="textarea_editor form-control" placeholder="Insert Amenities simplified">{{ old('amenities_simplified') }}</textarea>
                                                    </div>
                                                    @error('amenities_simplified')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="tab-inner-title">Description</div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="description" class="form-label">English</label>
                                                        <textarea id="description" name="description" class="textarea_editor form-control" placeholder="Insert description">{{ old('description') }}</textarea>
                                                        @error('description')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="description_traditional" class="form-label">Traditional</label>
                                                        <textarea id="description_traditional" name="description_traditional" class="textarea_editor form-control" placeholder="Insert description traditional">{{ old('description_traditional') }}</textarea>
                                                        @error('description_traditional')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label for="description_simplified" class="form-label">Simplified</label>
                                                        <textarea id="description_simplified" name="description_simplified" class="textarea_editor form-control" placeholder="Insert description simplified">{{ old('description_simplified') }}</textarea>
                                                        @error('description_simplified')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                        <input id="villa_id" name="villa_id" value="{{ $villa->id }}" type="hidden">
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="addVillaRoom" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add Rooms</button>
                                    <a href="{{ route('admin.villa.show',$villa->id) }}#rooms">
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
        </div>
    @endcan
    @include('partials.loading-form', ['id' => 'addVillaRoom'])
@endsection
