@section('title', __('messages.Hotels'))
@section('content')
    @extends('layouts.head')

    <div class="mobile-menu-overlay"></div>
    <div class="main-container">

        {{-- <div class="pd-ltr-20 "> --}}
        <div class="min-height-200px">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <h4>
                                {{ $hotel->type->type }} - {{ $hotel->name }}
                            </h4>
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="hotel">Hotels</a></li>
                                <li class="breadcrumb-item"><a href="hotel-{{ $hotel->id }}">{{ $hotel->name }}</a>
                                </li>
                                <li class="breadcrumb-item active" aria-current="page">Promo</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>

            {{-- Hotel --}}
            <div class="product-wrap card-box mb-30">
                <div class="product-detail-wrap mb-30">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="hotel-card">
                                <figure class="card-banner">
                                    <img src="{{ asset('storage/hotels/hotels-cover/' . $hotel->cover) }}"
                                        alt="{{ $hotel->name }}" loading="lazy">
                                </figure>
                                <div class="card-content">
                                    <h3 class="h3 card-title">{{ $hotel->name }}</h3>
                                    <div class="card-text">
                                        {!! $hotel->description !!}
                                    </div>
                                    <div class="card-text">
                                        <h5>Note:</h5>
                                        {!! $hotel->note !!}
                                    </div>
                                    <ul class="card-meta-list">
                                        <li class="card-meta-item">
                                            <div class="meta-box">
                                                <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
                                                <p class="text">{{ $hotel->region }}</p>
                                            </div>
                                        </li>
                                        <li class="card-meta-item">
                                            <div class="meta-box">
                                                <i class="icon-copy fa fa-globe" aria-hidden="true"></i>

                                                <a target="__blank"
                                                    href="https://{{ $hotel->web }}">{{ $hotel->web }}</a>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                <div class="card-price">
                                    <p>Start From</p>
                                    <p class="price">
                                        {{ "$" . $lowerprice }}
                                        <span>/Night</span>
                                    </p>


                                </div>

                            </div>

                        </div>
                    </div>
                </div>
                {{-- Hotel Rooms =================================================================================================================== --}}



                <div class="product-detail-wrap ">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <div class="row clearfix">

                                <div class="col-lg-9">
                                    <h5>Promo Price With Booking Period</h5>
                                    <div class="mentions">{{ 'checkin: ' . $checkin . ' checkout: ' . $checkout }}</div>
                                    <div class="mentions">{{ 'checkin: ' . $checkin . ' checkout: ' . $checkout }}</div>
                                    <div class="row clearfix m-b-30">


                                        <div class="col-lg-12">

                                            <table class="data-table table nowrap hover" style="width: 100%;">
                                                <thead>
                                                    <tr>
                                                        <th style="width: 5%; vertical-align: middle;">No</th>
                                                        <th class="table-plus datatable-nosort"
                                                            style="width: 10%; vertical-align: middle;">Room Name</th>
                                                        <th style="width: 10%; vertical-align: middle;">Stay Period</th>



                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($hotel->rooms as $no => $rooms)
                                                        @foreach ($rooms->promos as $promo)
                                                            <tr>
                                                                <td>{{ ++$no }}</td>
                                                                <td>{{ $rooms->rooms }}</td>
                                                                <td style="width: 10%;" class="text-center">
                                                                    {{ $promo->rooms_id }} <br>
                                                                    {{ date('m/d', strtotime($promo->periode_start)) . ' - ' . date('m/d', strtotime($promo->periode_end)) }}
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>


                                    </div>





                                </div>
                                <div class="col-md-3">



                                    <h5>Search Tools</h5>
                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <ul>
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    <form action="/hotel-promo/" method="POST" role="search"
                                            style="padding:0px;";>
                                            {{ csrf_field() }}
                                            <div class="form-group col-md-12">
                                                <label>Check In</label>
                                                <input name="checkin" id="checkin" wire:model="checkin"
                                                    class="form-control date-picker @error('checkin') is-invalid @enderror"
                                                     type="text"
                                                    value="{{ $checkin }}">
                                                @error('checkin')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <div class="form-group col-md-12">
                                                <label>Check Out</label>
                                                <input name="checkout" id="checkout" wire:model="dateout"
                                                    class="form-control date-picker @error('checkout') is-invalid @enderror"
                                                    type="text"
                                                    value="{{ $checkout }}">
                                                @error('checkout')
                                                    <span class="invalid-feedback">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                @enderror
                                            </div>
                                            <input type="hidden" name="hotelid" value="{{ $hotel->id }}">

                                            <div class="modal-footer">
                                                <button type="submit" class="btn btn-primary">
                                                    <i class='icon-copy fa fa-search' aria-hidden='true'></i>
                                                    Change Date
                                                </button>

                                            </div>

                                        </form>



                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            {{-- Promo ---------------------------------------------------------------------------------------------------- --}}



        </div>
        @include('layouts.footer')
    </div>
    </div>



@endsection
