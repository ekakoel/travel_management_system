@section('title', __('messages.Wedding'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title">
                            <i class="icon-copy dw dw-flower"></i> Edit Ceremony Venue Decoration</div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/weddings-admin">Vendors</a></li>
                                <li class="breadcrumb-item"><a href="/weddings-hotel-admin-{{ $hotel->id }}">{{ $hotel->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Edit {{ $decoration->name }}</li>
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
                                @include('layouts.attentions')
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">Detail Decoration</div>
                                </div>
                                <form id="updateDecorationCeremonyVenue" action="/fedit-decoration-ceremony-venue-{{ $decoration->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('PUT')
                                    <div class="row">
                                        {{-- <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <div class="dropzone">
                                                            <img id="old-cover" src="{{ asset('storage/hotels/weddings/decorations/'.$decoration->cover) }}" alt="{{ $decoration->name }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="cover" class="form-label">Cover Image </label><br>
                                                <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ $decoration->cover }}" onchange="updateCoverPreview(event)">
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div> --}}
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label for="add-cover-decoration-img-preview" class="form-label">Cover Preview</label>
                                                        <div class="dropzone">
                                                            <div id="add-cover-decoration-img-preview">
                                                                <img id="old-cover" src="{{ asset('storage/hotels/weddings/decorations/'.$decoration->cover) }}" alt="{{ $decoration->name }}">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="cover">Cover Image</label>
                                                <input type="file" name="cover" id="addCoverCeremonyDecorationPackage" onchange="addCoverCeremonyDecorationPreview(event)" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}" required>
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="cover" class="form-label">Status</label>
                                                <select id="status" name="status" class="form-control custom-select @error('status') is-invalid @enderror" required>
                                                    <option selected="{{ $decoration->status }}">{{ $decoration->status }}</option>
                                                    <option value="Active">Active</option>
                                                    <option value="Draft">Draft</option>
                                                </select>
                                                @error('status')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="name" class="form-label">Name </label>
                                                <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert decoration name!" value="{{ $decoration->name }}" required>
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="capacity" class="form-label">Capacity </label>
                                                <input type="number" min="1" id="capacity" name="capacity" class="form-control @error('capacity') is-invalid @enderror" placeholder="ex: 2" value="{{ $decoration->capacity }}" required>
                                                @error('capacity')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label for="price">Price <span>*</span></label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="text" id="price" name="price"  class="form-control @error('price') is-invalid @enderror" placeholder="Insert price!" value="{{ $decoration->price }}" required>
                                                    @error('price')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea id="description" name="description"  class="textarea_editor form-control border-radius-0 @error('description') is-invalid @enderror" placeholder="Insert some text ..." value="{{ $decoration->description }}">{!! $decoration->description !!}</textarea>
                                                @error('description')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="form-group">
                                                <label for="terms_and_conditions" class="form-label">Terms and Conditions</label>
                                                <textarea id="terms_and_conditions" name="terms_and_conditions"  class="textarea_editor form-control border-radius-0 @error('terms_and_conditions') is-invalid @enderror" placeholder="Insert some text ..." value="{{ $decoration->terms_and_conditions }}">{!! $decoration->terms_and_conditions !!}</textarea>
                                                @error('terms_and_conditions')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="updateDecorationCeremonyVenue" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                    <a href="/weddings-hotel-admin-{{ $hotel->id }}#ceremonyVenueDecorations">
                                        <button type="button"class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
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
                </div>
            </div>
        </div>
        <script>
            function updateCoverPreview(event) {
                var input = event.target;
                var reader = new FileReader();
                
                reader.onload = function() {
                    var dataURL = reader.result;
                    var imgElement = document.getElementById('old-cover');
                    imgElement.src = dataURL;
                };
                reader.readAsDataURL(input.files[0]);
            }
        </script>
        <script>
            function addCoverCeremonyDecorationPreview(event) {
                var input = event.target;
                var reader = new FileReader();
                reader.onload = function() {
                    var dataURL = reader.result;
                    var previewDiv = document.getElementById('add-cover-decoration-img-preview');
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
