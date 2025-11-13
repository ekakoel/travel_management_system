@section('title', __('messages.Weddings'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="info-action">
        @if (\Session::has('error'))
            <div class="alert alert-danger">
                <ul>
                    <li>{!! \Session::get('error') !!}</li>
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
    </div>
    <div class="main-container">
        <div class="pd-ltr-20">
            
            <div class="row">
                <div class="col-md-12">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="icon-copy fi-torso-business"></i><i class="icon-copy fi-torso-female"></i> @lang("messages.Wedding Packages")</div>
                        </div>
                        <form action="/wedding-search" method="POST" role="search";>
                            {{ csrf_field() }}
                            <div class="search-container flex-end">
                                <div class="search-item">
                                    <input type="text" class="form-control" name="hotel_name" placeholder="Search by hotel name" value="{{ old('hotel_name') }}">
                                </div>
                                <button type="submit" class="btn-search btn-primary"><i class='icon-copy fa fa-search' aria-hidden='true'></i> Search</button>
                            </div>
                        </form>
                        <div class="card-box-content">
                            @foreach ($weddings as $wedding)
                            @php
                                $hotel = $hotels->where('id',$wedding->hotel_id)->firstOrFail();
                            @endphp
                                <div class="card">
                                    <a href="wedding-{{ $wedding->code }}">
                                        <div class="card">
                                            <div class="image-container">
                                                <div class="first">
                                                    <div class="card-lable">
                                                        <div class="meta-box">
                                                            <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
                                                            <p class="text">{{ $hotel->name }} | <i class="icon-copy fa fa-users" aria-hidden="true"></i> {{ $wedding->capacity }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <a href="wedding-{{ $wedding->code }}">
                                                    <img src="{{ asset('storage/weddings/wedding-cover/' . $wedding->cover) }}"
                                                    class="img-fluid rounded thumbnail-image">
                                                </a>
                                                <a href="wedding-{{ $wedding->code }}">
                                                    <div class="card-detail-title">{{ $wedding->name }}</div>
                                                </a>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                        <div class="pagination">
                            <div class="pagination-panel">
                                {{ $weddings->links() }}
                            </div>
                            <div class="pagination-desk">
                                {{ $weddings->total() }} <span>@lang('messages.Wedding Package Available')</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @include('layouts.footer')
        </div>
    </div>
@endsection
