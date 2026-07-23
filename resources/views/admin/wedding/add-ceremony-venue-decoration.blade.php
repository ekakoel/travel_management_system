@section('title', __('messages.Order Wedding'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <x-backend.page-hero>
                        <x-slot name="heading">
                            <i class="icon-copy dw dw-flower"></i> Add Ceremony Venue Decoration
                        </x-slot>
                    </x-backend.page-hero>
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
                        <div class="col-md-4 mobile">
                            <div class="row">
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">Detail Decoration</div>
                                </div>
                                <form id="addDecorationCeremonyVenue" action="/fadd-decoration-ceremony-venue-{{ $hotel->id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="backend-form-field">
                                                        <div class="dropzone">
                                                            <div class="cover-preview-div">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="cover" class="form-label">Cover Image </label><br>
                                                <input type="file" name="cover" id="cover" class="backend-form-control @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}" required>
                                                @error('cover')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="name" class="form-label">Name </label>
                                                <input type="text" id="name" name="name" class="backend-form-control @error('name') is-invalid @enderror" placeholder="Insert decoration name!" value="{{ old('name') }}" required>
                                                @error('name')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="capacity" class="form-label">Capacity </label>
                                                <input type="number" min="1" id="capacity" name="capacity" class="backend-form-control @error('capacity') is-invalid @enderror" placeholder="ex: 2" value="{{ old('capacity') }}" required>
                                                @error('capacity')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-6 col-md-6">
                                            <div class="backend-form-field">
                                                <label for="price">Price <span>*</span></label>
                                                <div class="btn-icon">
                                                    <span>$</span>
                                                    <input type="text" id="price" name="price"  class="backend-form-control @error('price') is-invalid @enderror" placeholder="Insert price!" value="{{ old('price') }}" required>
                                                    @error('price')
                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="backend-form-field">
                                                <label for="description" class="form-label">Description</label>
                                                <textarea data-backend-richtext="true" id="description" name="description"  class="textarea_editor backend-form-control border-radius-0 @error('description') is-invalid @enderror" placeholder="Insert some text ..." value="{{ old('description') }}"></textarea>
                                                @error('description')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-12 col-md-12">
                                            <div class="backend-form-field">
                                                <label for="terms_and_conditions" class="form-label">Terms and Conditions</label>
                                                <textarea data-backend-richtext="true" id="terms_and_conditions" name="terms_and_conditions"  class="textarea_editor backend-form-control border-radius-0 @error('terms_and_conditions') is-invalid @enderror" placeholder="Insert some text ..." value="{{ old('terms_and_conditions') }}"></textarea>
                                                @error('terms_and_conditions')
                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </form>
                                <div class="card-box-footer">
                                    <button type="submit" form="addDecorationCeremonyVenue" class="backend-button backend-button-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Create</button>
                                    <a href="/weddings-hotel-admin-{{ $hotel->id }}#ceremonyVenueDecorations">
                                        <button type="button"class="backend-button backend-button-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 desktop">
                            <div class="row">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endcan
@endsection
