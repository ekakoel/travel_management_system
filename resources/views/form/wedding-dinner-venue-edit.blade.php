@section('title', __('messages.Weddings'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title">
                            <i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Dinner Venue
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/weddings-admin">Wedding Venue</a></li>
                                <li class="breadcrumb-item"><a href="/weddings-hotel-admin-{{ $hotel->id }}">{{ $hotel->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit {{ $dinner_venue->name }}</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="product-wrap">
                        
                        <div class="row">
                            <div class="col-md-8 m-b-18">
                                <div class="card-box p-b-18">
                                    <div class="card-box-title">
                                        <div class="subtitle">Dinner Venue</div>
                                    </div>
                                    <form id="editDinnerVenue" action="/fupdate-dinner-venue-{{ $dinner_venue->id }}" method="post" enctype="multipart/form-data" >
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="dropzone">
                                                            <div class="cover-preview-div">
                                                                <img src="{{ asset('storage/weddings/dinner-venues/'. $dinner_venue->cover)  }}" alt="{{ $dinner_venue->name }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="cover" class="form-label">Cover Image <span>*</span></label>
                                                    <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}">
                                                    @error('cover')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="status" class="form-label">Status <span>*</span></label>
                                                    <select name="status" class="custom-select custom-select col-12 @error('status') is-invalid @enderror" required>
                                                        <option selected value="{{ $dinner_venue->status }}">{{ $dinner_venue->status }}</option>
                                                        @if ($dinner_venue->status == 'Active')
                                                            <option value="Draft">Draft</option>
                                                        @else
                                                            <option value="Active">Active</option>
                                                        @endif
                                                    </select>
                                                    @error('status')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ $dinner_venue->name }}" required>
                                                    @error('name')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="capacity" class="form-label">Capacity</label>
                                                    <input type="number" min="1" name="capacity" class="form-control @error('capacity') is-invalid @enderror" placeholder="Capacity" value="{{ $dinner_venue->capacity }}" required>
                                                    @error('capacity')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="periode_start" class="form-label">Period Start</label>
                                                    <input type="text" name="periode_start" class="form-control date-picker @error('periode_start') is-invalid @enderror" placeholder="Select date" value="{{ $dinner_venue->periode_start }}" required>
                                                    @error('periode_start')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="periode_end" class="form-label">Period End</label>
                                                    <input type="text" name="periode_end" class="form-control date-picker @error('periode_end') is-invalid @enderror" placeholder="Select date" value="{{ $dinner_venue->periode_end }}" required>
                                                    @error('periode_end')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="markup" class="form-label">Markup</label>
                                                    <input type="number" name="markup" class="form-control @error('markup') is-invalid @enderror" placeholder="Markup" value="{{ $dinner_venue->markup }}">
                                                    @error('markup')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="publish_rate" class="form-label">Publish Rate</label>
                                                    <input type="number" name="publish_rate" class="form-control @error('publish_rate') is-invalid @enderror" placeholder="Publish Rate" value="{{ $dinner_venue->publish_rate }}">
                                                    @error('publish_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                    <label for="description" class="form-label">Description</label>
                                                    <textarea name="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Insert description" value="{{ $dinner_venue->description }}">{!! $dinner_venue->description !!}</textarea>
                                                    @error('description')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                    <label for="terms_and_conditions" class="form-label">Terms and Conditions</label>
                                                    <textarea name="terms_and_conditions" class="textarea_editor form-control @error('terms_and_conditions') is-invalid @enderror" placeholder="Insert terms_and_conditions" value="{{ $dinner_venue->terms_and_conditions }}">{!! $dinner_venue->terms_and_conditions !!}</textarea>
                                                    @error('terms_and_conditions')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                    </form>
                                    <div class="card-box-footer">
                                        <button type="submit" form="editDinnerVenue" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                        <a href="/weddings-hotel-admin-{{ $hotel->id }}#dinnerVenue">
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
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
                    
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
@endsection
