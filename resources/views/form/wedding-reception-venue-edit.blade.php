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
                            <i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Reception Venue
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/weddings-admin">Wedding Venue</a></li>
                                <li class="breadcrumb-item"><a href="/weddings-hotel-admin-{{ $hotel->id }}">{{ $hotel->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit Reception Venue</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="product-wrap">
                        <div class="row">
                            <div class="col-md-8 m-b-18">
                                <div class="card-box p-b-18">
                                    <div class="card-box-title">
                                        <div class="subtitle"><i class="icon-copy dw dw-pinwheel"></i> Reception Venue</div>
                                    </div>
                                    <form id="editReceptionVenue" action="/fupdate-reception-venue-{{ $reception_venue->id }}" method="post" enctype="multipart/form-data" >
                                        @csrf
                                        @method('PUT')
                                        <div class="row">
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="row">
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label for="cover-preview" class="form-label">Cover Image</label>
                                                            <div class="dropzone">
                                                                <div id="cover-img-preview">
                                                                    <img src="{{ asset('storage/weddings/reception-venues/'. $reception_venue->cover)  }}" alt="{{ $reception_venue->name }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="cover" class="form-label">Cover Image </label>
                                                    <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" onchange="updateCoverPreview(event)">
                                                    @error('cover')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="status" class="form-label">Status <span>*</span></label>
                                                    <select name="status" class="custom-select custom-select col-12 @error('status') is-invalid @enderror" required>
                                                        <option selected value="{{ $reception_venue->status }}">{{ $reception_venue->status }}</option>
                                                        @if ($reception_venue->status == 'Active')
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
                                                    <input type="text" min="1" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ $reception_venue->name }}" required>
                                                    @error('name')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="capacity" class="form-label">Capacity</label>
                                                    <input type="number" min="1" name="capacity" class="form-control @error('capacity') is-invalid @enderror" placeholder="Capacity" value="{{ $reception_venue->capacity }}" required>
                                                    @error('capacity')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="line-with-text">
                                                    <span class="line-text">Period</span>
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="periode_start" class="form-label">Periode Start</label>
                                                    <input type="text" name="periode_start" class="form-control date-picker @error('periode_start') is-invalid @enderror" placeholder="Periode Start" value="{{ $reception_venue->periode_start }}" required>
                                                    @error('periode_start')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="periode_end" class="form-label">Periode End</label>
                                                    <input type="text" name="periode_end" class="form-control date-picker @error('periode_end') is-invalid @enderror" placeholder="Periode End" value="{{ $reception_venue->periode_end }}" required>
                                                    @error('periode_end')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="line-with-text">
                                                    <span class="line-text">Price</span>
                                                </div>
                                            </div>
                                            
                                            <div class="col-6 col-md-6">
                                                <div class="form-group">
                                                    <label for="price">Price</label>
                                                    <div class="btn-icon">
                                                        <span>$</span>
                                                        <input type="text" id="price" name="price"  class="form-control @error('price') is-invalid @enderror" value="{{ $reception_venue->price }}" required>
                                                        @error('price')
                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                        @enderror
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12">
                                                <div class="line-with-text">
                                                    <span class="line-text">Additional Informations</span>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                    <label for="description" class="form-label">Descriptions</label>
                                                    <textarea name="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Insert description" value="{{ $reception_venue->description }}">{!! $reception_venue->description !!}</textarea>
                                                    @error('description')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-12">
                                                <div class="form-group">
                                                    <label for="terms_and_conditions" class="form-label">Terms and Conditions</label>
                                                    <textarea name="terms_and_conditions" class="textarea_editor form-control @error('terms_and_conditions') is-invalid @enderror" placeholder="Insert terms_and_conditions" value="{{ $reception_venue->terms_and_conditions }}">{!! $reception_venue->terms_and_conditions !!}</textarea>
                                                    @error('terms_and_conditions')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                    </form>
                                    <div class="card-box-footer">
                                        <button type="submit" form="editReceptionVenue" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                        <a href="/weddings-hotel-admin-{{ $hotel->id }}#receptionVenue">
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
        <script>
            function updateCoverPreview(event) {
                var input = event.target;
                var reader = new FileReader();
                reader.onload = function() {
                    var dataURL = reader.result;
                    var previewDiv = document.getElementById('cover-img-preview');
                    previewDiv.innerHTML = '';
                    var imgElement = document.createElement('img');
                    imgElement.src = dataURL;
                    imgElement.className = 'img-fluid rounded';
                    previewDiv.appendChild(imgElement);
                };
                reader.readAsDataURL(input.files[0]);
            }
        </script>
    @endcan
@endsection
