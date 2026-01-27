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
                            <i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Lunch Venue
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/weddings-admin">Wedding Venue</a></li>
                                <li class="breadcrumb-item"><a href="/weddings-hotel-admin-{{ $hotel->id }}">{{ $hotel->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Lunch Venue</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="product-wrap">
                        <div class="row">
                            <div class="col-md-8 m-b-18">
                                <div class="card-box p-b-18">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy dw dw-food-cart"></i> Lunch Venue</div>
                                    </div>
                                    <form id="editLunchVenue" action="/fupdate-lunch-venue-{{ $lunch_venue->id }}" method="post" enctype="multipart/form-data" >
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="dropzone">
                                                            <div class="cover-preview-div">
                                                                <img src="{{ asset('storage/weddings/lunch-venues/'. $lunch_venue->cover)  }}" alt="{{ $lunch_venue->name }}">
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
                                                        <option selected value="{{ $lunch_venue->status }}">{{ $lunch_venue->status }}</option>
                                                        @if ($lunch_venue->status == 'Active')
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
                                            <div class="col-12 col-md-12">
                                                <div class="form-group">
                                                    <label for="name" class="form-label">Name</label>
                                                    <input type="text" min="1" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ $lunch_venue->name }}" required>
                                                    @error('name')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="min_capacity" class="form-label">Minimum Capacity</label>
                                                    <input type="number" min="1" name="min_capacity" class="form-control @error('min_capacity') is-invalid @enderror" placeholder="Capacity" value="{{ $lunch_venue->min_capacity }}" required>
                                                    @error('min_capacity')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="max_capacity" class="form-label">Maximum Capacity</label>
                                                    <input type="number" min="1" name="max_capacity" class="form-control @error('max_capacity') is-invalid @enderror" placeholder="Capacity" value="{{ $lunch_venue->max_capacity }}" required>
                                                    @error('max_capacity')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="periode_start" class="form-label">Periode Start</label>
                                                    <input type="text" name="periode_start" class="form-control date-picker @error('periode_start') is-invalid @enderror" placeholder="Periode Start" value="{{ $lunch_venue->periode_start }}" required>
                                                    @error('periode_start')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="periode_end" class="form-label">Periode End</label>
                                                    <input type="text" name="periode_end" class="form-control date-picker @error('periode_end') is-invalid @enderror" placeholder="Periode End" value="{{ $lunch_venue->periode_end }}" required>
                                                    @error('periode_end')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="markup" class="form-label">Markup</label>
                                                    <input type="number" name="markup" class="form-control @error('markup') is-invalid @enderror" placeholder="Markup" value="{{ $lunch_venue->markup }}">
                                                    @error('markup')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="publish_rate" class="form-label">Publish Rate</label>
                                                    <input type="number" name="publish_rate" class="form-control @error('publish_rate') is-invalid @enderror" placeholder="Publish Rate" value="{{ $lunch_venue->publish_rate }}">
                                                    @error('publish_rate')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                    <label for="terms_and_conditions" class="form-label">Terms and Conditions</label>
                                                    <textarea name="terms_and_conditions" class="textarea_editor form-control @error('terms_and_conditions') is-invalid @enderror" placeholder="Insert terms_and_conditions" value="{{ $lunch_venue->terms_and_conditions }}">{!! $lunch_venue->terms_and_conditions !!}</textarea>
                                                    @error('terms_and_conditions')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                    <label for="description" class="form-label">Descriptions</label>
                                                    <textarea name="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Insert description" value="{{ $lunch_venue->description }}">{!! $lunch_venue->description !!}</textarea>
                                                    @error('description')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                    </form>
                                    <div class="card-box-footer">
                                        <button type="submit" form="editLunchVenue" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                        <a href="/weddings-hotel-admin-{{ $hotel->id }}#lunchVenue">
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
