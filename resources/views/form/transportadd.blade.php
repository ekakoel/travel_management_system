@section('title', __('messages.Transports'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title">
                            <i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Transportation
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                <li class="breadcrumb-item"><a href="/transports-admin">Transportation</a></li>
                                <li class="breadcrumb-item active">Add Transportation</li>
                            </ol>
                        </nav>
                    </div>
                   
                    <div class="row">
                        {{-- ATTENTIONS --}}
                        <div class="col-md-4 mobile">
                            <div class="row">
                                @include('layouts.attentions')
                            </div>
                        </div>
                        <div class="col-md-8 m-b-18">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title"><i class="fa fa-plus"></i> Add New Transport</div>
                                </div>
                                <form id="create-transport" action="/fadd-transport" method="post" enctype="multipart/form-data" id="my-awesome-dropzone">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6 col-md-6">
                                                     <div class="form-group">
                                                        <label class="p-b-8">Cover Image <span>*</span></label>
                                                        <div class="dropzone text-center pd-20">
                                                            <div class="cover-preview-div">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}" required>
                                                                @error('cover')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="name">Name</label>
                                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert transport name" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="brand" >Brand<span> *</span></label>
                                                <select id="brand" name="brand" class="custom-select @error('brand') is-invalid @enderror" required>
                                                    <option selected value="">Select brand</option>
                                                    @foreach ($brand as $brand)
                                                        <option value="{{ $brand->brand }}">{{ $brand->brand }}</option>
                                                    @endforeach
                                                </select>
                                                @error('brand')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="type">Type<span>*</span></label>
                                                <select id="type" name="type" class="custom-select col-12 @error('type') is-invalid @enderror" required>
                                                    <option selected value="">Select Type</option>
                                                    @foreach ($type as $type)
                                                        <option value="{{ $type->type }}">{{ $type->type }}</option>
                                                    @endforeach
                                                </select>
                                                @error('type')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="capacity" >Capacity</label>
                                                <input type="number" id="capacity" name="capacity" value="{{ old('capacity') }}" class="form-control @error('capacity') is-invalid @enderror" placeholder="Insert capacity" required>
                                                @error('capacity')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="description">Description<span>*</span></label>
                                                <textarea id="description" name="description" class="textarea_editor form-control border-radius-0" placeholder="Insert description" value="{{ old('description') }}" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="include" >Include<span>*</span></label>
                                                <textarea id="include" name="include" class="textarea_editor form-control border-radius-0" placeholder="Insert include" value="{{ old('include') }}" required></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="cancellation_policy">Cancellation Policy</label>
                                                <textarea id="cancellation_policy" name="cancellation_policy" class="textarea_editor form-control border-radius-0" placeholder="Insert cancellation policy" value="{{ old('cancellation_policy') }}"></textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="additional_info">Additional Information</label>
                                                <textarea id="additional_info" name="additional_info" class="textarea_editor form-control border-radius-0" placeholder="Insert additional information" value="{{ old('additional_info') }}"></textarea>
                                            </div>
                                        </div>
                                            <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                            <input id="page" name="page" value="add-tour" type="hidden">
                                            <input id="initial_state" name="initial_state" value="" type="hidden">
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="create-transport" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add Transportation</button>
                                    <a href="/transports-admin">
                                        <button type="button"class="btn btn-danger"><i class="icon-copy fa fa-remove" aria-hidden="true"></i> Cancel</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        {{-- ATTENTIONS --}}
                        <div class="col-md-4 desktop">
                            <div class="row">
                                @include('layouts.attentions')
                            </div>
                        </div>
                    </div>
                    @include('layouts.footer')
                    
                </div>
            </div>
        </div>
    @endcan
@endsection
