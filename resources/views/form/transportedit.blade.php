@section('title', __('messages.Transports'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title"><i class="icon-copy fa fa-car" aria-hidden="true"></i> Transportation Edit
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                <li class="breadcrumb-item"><a href="/transports-admin">Transportation</a></li>
                                <li class="breadcrumb-item"><a href="/detail-transport-{{ $transport->id }}">{{ $transport->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Transportation</li>
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
                                    <div class="title">{{ "Edit ". $transport->name }}</div>
                                </div>
                                <form id="updateTransport" action="/fupdate-transport/{{ $transport->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="dropzone">
                                                        <div class="cover-preview-div">
                                                            <img class="m-b-18" src="{{ asset('storage/transports/transports-cover/' . $transport->cover)  }}" alt="{{ $transport->name }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="cover" class="form-label">Cover Image </label>
                                                <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="cover" class="form-label">Status </label>
                                                <select id="status" name="status" class="custom-select @error('status') is-invalid @enderror" required>
                                                    <option selected="{{ $transport->status }}">{{ $transport->status }}</option>
                                                    <option value="Active">Active</option>
                                                    <option value="Draft">Draft</option>
                                                    <option value="Archived">Archived</option>
                                                </select>
                                                @error('status')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Name </label>
                                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Tour Package Name" value="{{ $transport->name }}" required>
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="brand" class="form-label">Brand </label>
                                                <select id="brand" name="brand" class="custom-select @error('brand') is-invalid @enderror" required>
                                                    <option selected value="{{ $transport->brand }}">{{ $transport->brand }}</option>
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
                                                <label for="type" class="form-label">Type </label>
                                                <select id="type" name="type" class="custom-select  @error('type') is-invalid @enderror" required>
                                                    <option selected value="{{ $transport->type }}">{{ $transport->type }}</option>
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
                                                <label for="capacity" class="form-label">Capacity </label>
                                                <input type="number" id="capacity" name="capacity" value="{{ $transport->capacity }}" class="form-control @error('capacity') is-invalid @enderror" placeholder="Capacity" required>
                                                @error('capacity')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="description" class="form-label">Description </label>
                                                <textarea id="description" name="description" class="textarea_editor form-control">{!! $transport->description !!}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="include" class="form-label">Include </label>
                                                <textarea id="include" name="include" class="textarea_editor form-control">{!! $transport->include !!}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="cancellation_policy" class="form-label">Cancellation Policy</label>
                                                <textarea id="cancellation_policy" name="cancellation_policy" class="textarea_editor form-control">{!! $transport->cancellation_policy !!}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="additional_info" class="form-label">Additional Information</label>
                                                <textarea id="additional_info" name="additional_info" class="textarea_editor form-control">{!! $transport->additional_info !!}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-12 text-right m-t-8 m-b-18">
                                            <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                            <input id="page" name="page" value="admin-tour-edit" type="hidden">
                                            <input id="initial_state" name="initial_state" value="{{ $transport->status }}" type="hidden">
                                           
                                        </div>
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="updateTransport" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Save</button>
                                    <a href="detail-transport-{{ $transport['id'] }}">
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
