{{-- @include('layouts.loader') --}}
@section('title', __('messages.Hotel Reservation'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <x-backend.page-hero>
                    <x-slot name="heading">
                        Reservation {{ $reservation->rsv_no }}
                    </x-slot>
                </x-backend.page-hero>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="product-wrap card-box mb-30 p-b-18">
                            <div class="product-detail-wrap">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="title">Hotels</div>
                                        @if (\Session::has('success'))
                                            <div class="alert alert-success">
                                                <ul>
                                                    <li>{!! \Session::get('success') !!}</li>
                                                </ul>
                                            </div>
                                        @endif
                                        <hr class="hr-form">
                                    </div>
                                    <div class="col-md-12">
                                        <div class="input-group row">
                                            <div class="col-md-4 p-b-8">
                                                <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                                <input id="searchHotelName" type="text" onkeyup="searchHotelName()" class="backend-form-control" name="search-byno" placeholder="Sort by hotel name">
                                            </div>
                                        </div>
                                        @if (count($hotels) > 0)
                                        <table id="tbord" class="data-table table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%">No</th>
                                                    <th style="width: 25%">Hotels</th>
                                                    <th style="width: 50%">Rooms & Suite</th>
                                                    <th style="width: 10%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($hotels as $no=>$hotel)

                                                    <tr>
                                                        <td>{{ ++$no }}</td>
                                                        <td>
                                                            {{ $hotel->name }}
                                                        </td>
                                                        <td>
                                                           @foreach ($hotel->rooms as $room)
                                                               {{ $room->rooms.", " }}
                                                           @endforeach
                                                        </td>

                                                        <td class="text-right">
                                                            <a href="#" data-toggle="modal" data-target="#add-hotel-{{ $hotel->id }}">
                                                                <i class="icon-copy fa fa-plus-circle p-r-18" data-toggle="tooltip" data-placement="left" title="Add Accommodation" aria-hidden="true"></i>
                                                            </a>
                                                            {{-- Modal Add Accommodation --------------------------------------------------------------------------------------------------------------- --}}
                                                            <div class="modal fade" id="add-hotel-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content text-left">
                                                                        <div class="product-detail-wrap">

                                                                                <div class="row">

                                                                                        <div class="col-md-12">
                                                                                            <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i>{{ $hotel->name }} - Add to Reservation</div>
                                                                                        </div>
                                                                                        <div class="col-12 text-right">
                                                                                            <hr class="form-hr">
                                                                                        </div>
                                                                                </div>
                                                                                <form action="/fadd-accommodation" method="post" enctype="multipart/form-data">
                                                                                    @csrf
                                                                                    @method('put')
                                                                                    <div class="row">
                                                                                        <div class="col-sm-6">
                                                                                            <div class="backend-form-field row">
                                                                                                <label for="servicename" class="col-sm-12 col-md-12 col-form-label">Check in - Check out <span>*</span></label>
                                                                                                <div class="col-sm-12">
                                                                                                    <input readonly name="checkincout" class="backend-form-control @error('checkincout') is-invalid @enderror" type="text" placeholder="Select date" required>
                                                                                                    @error('checkincout')
                                                                                                        <span class="invalid-feedback">
                                                                                                            {{ $message }}
                                                                                                        </span>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-3">
                                                                                            <div class="backend-form-field row">
                                                                                                <label for="servicename" class="col-sm-12 col-md-12 col-form-label">Rooms <span>*</span></label>
                                                                                                <div class="col-sm-12">
                                                                                                    <select name="servicename" class="backend-form-control @error('servicename') is-invalid @enderror" required>
                                                                                                        <option selected value="">Select Room</option>
                                                                                                        @foreach ($hotel->rooms as $room)
                                                                                                            <option value="{{ $room->rooms }}">{{ $room->rooms }}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @error('servicename')
                                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-3">
                                                                                            <div class="backend-form-field row">
                                                                                                <label for="extra_bed" class="col-sm-12 col-md-12 col-form-label">Extra Bed</label>
                                                                                                <div class="col-sm-12">
                                                                                                    <select name="extra_bed" class="backend-form-control @error('extra_bed') is-invalid @enderror" value="{{ old('extra_bed') }}">
                                                                                                        <option selected value="">Select</option>
                                                                                                        <option value="Yes">Yes</option>
                                                                                                        <option value="No">No</option>
                                                                                                    </select>
                                                                                                    @error('extra_bed')
                                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-3">
                                                                                            <div class="backend-form-field row">
                                                                                                <label for="guest_1" class="col-sm-12 col-md-12 col-form-label">Guest 1 <span>*</span></label>
                                                                                                <div class="col-sm-12">
                                                                                                    <select name="guest_1" class="backend-form-control @error('guest_1') is-invalid @enderror" required>
                                                                                                        <option selected value="">Select Guest</option>
                                                                                                        @foreach ($guests as $guest)
                                                                                                            <option value="{{ $guest->id }}">{{ $guest->name }}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @error('guest_1')
                                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-3">
                                                                                            <div class="backend-form-field row">
                                                                                                <label for="guest_2" class="col-sm-12 col-md-12 col-form-label">Guest 2</label>
                                                                                                <div class="col-sm-12">
                                                                                                    <select name="guest_2" class="backend-form-control @error('guest_2') is-invalid @enderror">
                                                                                                        <option selected value="">Select Guest</option>
                                                                                                        @foreach ($guests as $guest)
                                                                                                            <option value="{{ $guest->id }}">{{ $guest->name }}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @error('guest_2')
                                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-3">
                                                                                            <div class="backend-form-field row">
                                                                                                <label for="guest_3" class="col-sm-12 col-md-12 col-form-label">Guest 3</label>
                                                                                                <div class="col-sm-12">
                                                                                                    <select name="guest_3" class="backend-form-control @error('guest_3') is-invalid @enderror">
                                                                                                        <option selected value="">Select Guest</option>
                                                                                                        @foreach ($guests as $guest)
                                                                                                            <option value="{{ $guest->id }}">{{ $guest->name }}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @error('guest_3')
                                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                        <div class="col-sm-3">
                                                                                            <div class="backend-form-field row">
                                                                                                <label for="guest_4" class="col-sm-12 col-md-12 col-form-label">Guest 4</label>
                                                                                                <div class="col-sm-12">
                                                                                                    <select name="guest_4" class="backend-form-control @error('guest_4') is-invalid @enderror">
                                                                                                        <option selected value="">Select Guest</option>
                                                                                                        @foreach ($guests as $guest)
                                                                                                            <option value="{{ $guest->id }}">{{ $guest->name }}</option>
                                                                                                        @endforeach
                                                                                                    </select>
                                                                                                    @error('guest_4')
                                                                                                        <div class="alert-form">{{ $message }}</div>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>

                                                                                        <div class="col-sm-12 col-md-12 text-right">
                                                                                            <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                                                                            <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                                                                            <button type="submit" class="backend-button backend-button-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                                            <button type="button" class="backend-button backend-button-danger" data-dismiss="modal">Cancel</button>
                                                                                        </div>
                                                                                    </div>
                                                                                </form>

                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                    </tr>

                                                @endforeach
                                            </tbody>
                                        </table>
                                        @else
                                            <div class="notification"><i class="icon-copy fa fa-info" aria-hidden="true"></i> Order is not available, you can add an order on the order page</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="product-wrap card-box mb-30">
                            <div class="product-detail-wrap">
                                <div class="row">
                                    <div class="col-md-12">

                                        <table class="data-table table table-bordered">
                                            <thead>
                                                <tr>
                                                    <th style="width: 20%" scope="col">Check In - Check Out</th>
                                                    <th style="width: 15%" scope="col">Hotel / Villas</th>
                                                    <th style="width: 10%" scope="col">Unit</th>
                                                    <th style="width: 10%" scope="col">Extra Bed</th>
                                                    <th style="width: 10%" scope="col">Room & Suites</th>
                                                    <th style="width: 20%" scope="col">Inclusion</th>
                                                    <th style="width: 10%" scope="col">Phone</th>
                                                    <th style="width: 5%" scope="col">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-md-12 text-right">

                                        <a href="/reservation-{{ $reservation->id }}"><button type="button" class="backend-button backend-button-secondary m-b-8"><i class="icon-copy fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>







					<div class="col-md-4">
                        <div class="card-box mb-30">
                            <div class="banner-right">
                                <div class="title">Attention!</div>
                                <p>1. On this page you can add an order to the reservation</p>
                                <p>2. Find the desired order using sorting</p>
                                <p>3. If the order is not available, then you can add it on the order page</p>
                            </div>
                        </div>
                        @if ($reservation->msg != "")
                            <div class="product-wrap card-box mb-30">
                                <b>Admin Notes</b> <br>
                                <div class="trackrecord">
                                    <p>{{ $reservation->msg }}</p>
                                </div>
                            </div>
                        @else
                        @endif
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>
    @endcan
@endsection
