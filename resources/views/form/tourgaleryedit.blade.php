@section('title', __('messages.Tour'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>Edit Tour Package - {{ $tours['name'] }}</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="tours-admin">Admin Tour Package</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Detail</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-md-6">
                    <div class="card-box mb-30 pd-20 row">
                        @if (count($tours->images) > 0)
                            @foreach ($tours->images as $img)
                                <div class="col-sm-6 col-md-6">
                                    <form action="/fdelete-tour-img/{{ $img->id }}" method="post">
                                        <button class="btn text-right" style="color: white; position: absolute; right: 30; z-index:99;">X</button>
                                        @csrf
                                        @method('delete')
                                    </form>
                                    <img src="/images/tours/{{ $img->image }}" class="img-responsive" style="max-width: 100%; padding:0 8px 8px 8px;">
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
                <div class="col-md-6 col-md-6">
                    <form action="/fupdate-tour/{{ $tours->id }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        {{ csrf_field() }}
                        <div class="card-box mb-30 pd-20">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" name="name" value="{{ $tours->name }}">
                                        <input type="hidden" name="type" value="{{ $tours->type }}">
                                        <input type="hidden" name="duration" value="{{ $tours->duration }}">
                                        <input type="hidden" name="description" value="{{ $tours->description }}">
                                        <input type="hidden" name="itinerary" value="{{ $tours->itinerary }}">
                                        <input type="hidden" name="include" value="{{ $tours->include }}">
                                        <input type="hidden" name="note" value="{{ $tours->note }}">
                                        <input type="hidden" name="price" value="{{ $tours->price }}">
                                        <input type="hidden" name="qty" value="{{ $tours->qty }}">
                                        <input type="hidden" name="status" value="{{ $tours->status }}">
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                        <input type="hidden" name="cover" value="{{ $tours->cover }}">
                                        <div class="col-md-12 mb-30">
                                            <div class="dropzone mt-1 text-center pd-20">
                                                <div class="images-preview-div">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="file" name="images[]" id="images"
                                                class="@error('images[]') is-invalid @enderror" placeholder="Choose images"
                                                value="{{ $tours->images }}" multiple>
                                            @error('images[]')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="text-right">
                                            <a href="tour-admin-detail-{{ $tours['id'] }}"><button
                                                    type="button"class="btn btn-danger">Cancel</button></a>
                                            <button type="submit" class="btn btn-info">Update Galery</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.footer')
