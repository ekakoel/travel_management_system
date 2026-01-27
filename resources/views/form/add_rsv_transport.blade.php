@section('title', __('messages.Reservation'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-7 col-sm-7">
                            <div class="title">
                                <h4>Reservation {{ $reservation->rsv_no }}</h4>
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="reservation">Reservations</a></li>
                                    <li class="breadcrumb-item"><a href="reservation-{{ $reservation->id }}">Detail Reservation</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Add Transport</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-lg-8">
                        <div class="product-wrap card-box mb-30">
                            <div class="product-detail-wrap">
                                <div class="row">
                                    <div class="col-md-12">
                                        <h4>Add Order</h4>
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
                                                <input id="searchOrderNo" type="text" onkeyup="searchOrderNo()" class="form-control" name="search-byno" placeholder="Sort by Reservation No..">
                                            </div>
                                            <div class="col-md-4 p-b-8">
                                                <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                                <input id="searchByAgent" type="text" onkeyup="searchByAgent()" class="form-control" name="search-byno" placeholder="Sort by Agent..">
                                            </div>
                                            <div class="col-md-4 p-b-8">
                                                <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                                <input id="searchByGuest" type="text" onkeyup="searchByGuest()" class="form-control" name="search-byno" placeholder="Sort by Guest..">
                                            </div>
                                           
                                        </div>
                                        @if (count($orders) > 0)
                                        <table id="tbord" class="data-table table">
                                            <thead>
                                                <tr>
                                                    <th style="width: 20%">Order No.</th>
                                                    <th style="width: 15%">Service</th>
                                                    <th style="width: 15%">Agent</th>
                                                    <th style="width: 20%">Guest</th>
                                                    <th style="width: 5%">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($orders as $order)
                                                
                                                    <tr>
                                                        <td>
                                                            <b>{{ $order->orderno }}</b><br>
                                                            {{ $order->service_type }}<br>
                                                            {{ 'Pickup date: '.dateFormat($order->checkin) }}<br>
                                                        </td>
                                                        <td>
                                                            <b>{{ $order->service }}</b><br>
                                                            {{ $order->servicename }}<br>
                                                            {{ $order->subservice }}
                                                        </td>
                                                        <td>{{ $order->name }}</td>
                                                        <td>{{ $order->guest_detail }}<br>
                                                            {{ $order->number_of_guests." Guests" }}
                                                        </td>

                                                        
                                                        <td>
                                                            <form action="/fupdate-accommodation/{{ $order->id }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('put')
                                                                <input type="hidden" name="rsv_id" value="{{ $reservation->id }}">
                                                                <input type="hidden" name="service" value="{{ $order->orderno }}">
                                                                <input type="hidden" name="author" value="{{ Auth::User()->id }}">
                                                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                                                <input type="hidden" name="catatan" value="Add order">
                                                                
                                                                <button type="submit" class="btn btn-info"><i class="icon-copy fa fa-plus" aria-hidden="true"></i>Add</button>
                                                            </form> 
                                                        </td>
                                                    </tr>
                                                
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @else
                                            <div class="notification"><i class="icon-copy fa fa-info" aria-hidden="true"></i> Order is not available, you can add an order on the order page</div>
                                        @endif
                                    </div>
                                    <div class="col-md-12 text-right">

                                        <a href="/reservation-{{ $reservation->id }}"><button type="button" class="btn btn-dark m-b-8"><i class="icon-copy fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
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
