@section('title', __('messages.Hotels'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>Edit Hotel - {{ $hotels['name'] }}</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="admin-hotels">Admin Hotel</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Detail</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-md-6">
                    <div class="card-box mb-30 pd-20 row">
                        @if (count($hotels->images) > 0)
                            @foreach ($hotels->images as $img)
                                <div class="col-sm-6 col-md-6">
                                    <form action="/fdelete-hotel-img/{{ $img->id }}" method="post">
                                        <button class="btn text-right"
                                            style="color: white; position: absolute; right: 30; z-index:99;">X</button>
                                        @csrf
                                        @method('delete')
                                    </form>
                                    <img src="{{ asset('storage/hotels/hotels-galery/' . $img->image) }}" class="img-responsive"
                                        style="max-width: 100%; padding:0 8px 8px 8px;">
                                </div>
                            @endforeach
                        @else
                            <i>The hotel doesn't have a photo gallery yet!</i>
                        @endif
                    </div>
                </div>


                <div class="col-md-6 col-md-6">
                    <form action="/fupdate-hotel/{{ $hotels->id }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        {{ csrf_field() }}
                        <div class="card-box mb-30 pd-20">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">


                                 

                                        <input type="hidden" name="name" value="{{ $hotels->name }}">
                                        <input type="hidden" name="web" value="{{ $hotels->web }}">
                                        <input type="hidden" name="region" value="{{ $hotels->region }}">
                                        <input type="hidden" name="contract" value="{{ $hotels->contract }}">
                                        <input type="hidden" name="address" value="{{ $hotels->address }}">
                                        <input type="hidden" name="contact_person" value="{{ $hotels->contact_person }}">
                                        <input type="hidden" name="description" value="{{ $hotels->description }}">
                                        <input type="hidden" name="facility" value="{{ $hotels->facility }}">
                                        <input type="hidden" name="note" value="{{ $hotels->note }}">
                                        <input type="hidden" name="phone" value="{{ $hotels->phone }}">
                                        <input type="hidden" name="status" value="{{ $hotels->status }}">
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}" >
                                        <input type="hidden" name="cover" value="{{ $hotels->cover }}">

                                        <div class="col-md-12 mb-30">
                                            <div class="dropzone mt-1 text-center pd-20">
                                                <div class="images-preview-div">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="file" name="images[]" id="images"
                                                class="@error('images[]') is-invalid @enderror" placeholder="Choose images"
                                                value="{{ $hotels->images }}" multiple>
                                            @error('images[]')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="text-right">
                                            <a href="/detail-hotel-{{ $hotels['id'] }}"><button
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
    </div>
    </div>
