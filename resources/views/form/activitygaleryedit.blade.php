@section('title', __('messages.Activity'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>Edit Activity - {{ $activities['name'] }}</h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="activities-admin">Admin Activity</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Detail</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 col-md-6">
                    <div class="card-box mb-30 pd-20 row">
                        @if (count($activities->images) > 0)
                            @foreach ($activities->images as $img)
                                <div class="col-sm-6 col-md-6">
                                    <form action="/fdelete-activity-img/{{ $img->id }}" method="post">
                                        <button class="btn text-right"
                                            style="color: white; position: absolute; right: 30; z-index:99;">X</button>
                                        @csrf
                                        @method('delete')
                                    </form>
                                    <img src="{{ asset ('storage/activities/activities-images/' . $img->image) }}" alt="{{ $activities->name }}">
                                    {{-- <img src="/images/activities/{{ $img->image }}" class="img-responsive"
                                        style="max-width: 100%; padding:0 8px 8px 8px;"> --}}
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>


                <div class="col-md-6 col-md-6">
                    <form action="/fupdate-activity/{{ $activities->id }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        {{ csrf_field() }}
                        <div class="card-box mb-30 pd-20">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <input type="hidden" name="name" value="{{ $activities->name }}">
                                        <input type="hidden" name="type" value="{{ $activities->type }}">
                                        <input type="hidden" name="location" value="{{ $activities->location }}">
                                        <input type="hidden" name="duration" value="{{ $activities->duration }}">
                                        <input type="hidden" name="description" value="{{ $activities->description }}">
                                        <input type="hidden" name="itinerary" value="{{ $activities->itinerary }}">
                                        <input type="hidden" name="include" value="{{ $activities->include }}">
                                        <input type="hidden" name="note" value="{{ $activities->note }}">
                                        <input type="hidden" name="price" value="{{ $activities->price }}">
                                        <input type="hidden" name="additional_info" value="{{ $activities->additional_info }}">
                                        <input type="hidden" name="qty" value="{{ $activities->qty }}">
                                        <input type="hidden" name="status" value="{{ $activities->status }}">
                                        <input type="hidden" name="author" value="{{ Auth::user()->id }}" >
                                        <input type="hidden" name="cover" value="{{ $activities->cover }}">

                                        <div class="col-md-12 mb-30">
                                            <div class="dropzone mt-1 text-center pd-20">
                                                <div class="images-preview-div">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <input type="file" name="images[]" id="images"
                                                class="@error('images[]') is-invalid @enderror" placeholder="Choose images"
                                                value="{{ $activities->images }}" multiple>
                                            @error('images[]')
                                                <div class="alert alert-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="text-right">
                                            <a href="/detail-activity-{{ $activities['id'] }}"><button
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
