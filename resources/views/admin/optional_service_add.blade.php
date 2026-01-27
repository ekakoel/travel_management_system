@php
    $from = $order->checkin;
    $dur = $order->duration + 1;
    $date_stay = [];
    for ($a=0; $a < $dur ; $a++) { 
        $date_stay[$a] = $from;
        $from = date('Y-m-d',strtotime('+1 days',strtotime($from)));
    }
    if ($optional_rate_orders != "") {
       
        $service_date = json_decode($optional_rate_orders->service_date);
        $oro_sd = json_decode($optional_rate_orders->optional_rate_id);
        $oro_nog = json_decode($optional_rate_orders->number_of_guest);
        $oro = json_decode($optional_rate_orders->number_of_guest);
        $optional_rate_id = json_decode($optional_rate_orders->optional_rate_id);
        
    }
    
@endphp
@section('title', __('messages.Add Optional Service'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title"><i class="icon-copy fa fa-tags"></i>&nbsp; Edit Additional Charge</div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="orders">Order</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:history.back()">{{ $order->orderno }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Edit Additional Charges</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
               
                <div class="row">
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 mobile">
                        <div class="row">
                            @include('admin.usd-rate')
                            @include('layouts.attentions')
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-box mb-18 p-b-18">
                            <div class="page-subtitle">Additional Charges Available </div>
                                <table class="data-table table nowrap" >
                                    <thead>
                                        <tr>
                                            <th style="width: 10%;">No</th>
                                            <th style="width: 10%;">Type</th>
                                            <th style="width: 5%;">Service</th>
                                            <th style="width: 15%;">Description</th>
                                            <th style="width: 10%;">Price</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($optional_services as $n_list=>$optional_service)
                                        @php
                                            $or_usd = ceil(($optional_service->contract_rate / $usdrates->rate)+$optional_service->markup);
                                            $or_pajak = ceil(($or_usd * $tax->tax)/100);
                                            $or_pub_price = $or_usd + $or_pajak;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div class="table-service-name">{{ ++$n_list }}</div>
                                            </td>
                                            <td>
                                                <div class="table-service-name">{{ $optional_service->type }}</div>
                                            </td>
                                            <td>
                                                <div class="table-service-name">{{ $optional_service->name }}</div>
                                            </td>
                                            
                                            <td>
                                                <div class="table-service-name">{{ $optional_service->description }}</div>
                                            </td>
                                            <td>
                                                <div class="table-service-name">{{ currencyFormatUsd($or_pub_price)." /pax" }}</div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            <div class="page-subtitle">Additional Charge</div>
                            @if ($optional_rate_orders != "")
                            <p class="p-b-18"><i>Your order will be displayed here!</i></p>                                
                            @endif
                            @if ($optional_rate_orders != "")
                                <form action="/fadmin-update-optional-service-order-{{ $optional_rate_orders->order_id }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                            @else
                                <form action="/fadmin-add-optional-service-order" method="post" enctype="multipart/form-data">
                                    @csrf
                            @endif
                                <div class="row">
                                    <div class="col-md-12">
                                        <ol>
                                            @if ($optional_rate_orders != "" )
                                                @if ($oro_sd != "")
                                                    @php
                                                        $s = count($oro_sd);
                                                    @endphp
                                                @else
                                                    @php
                                                        $s = 0;
                                                    @endphp
                                                @endif
                                                
                                                @for ($i = 0; $i < $s; $i++)
                                                @php
                                                    $optional_service_name = $optional_services->where('id',$optional_rate_id[$i])->first();
                                                @endphp
                                                    <li class="m-b-8">
                                                        <div class="control-group">
                                                            <div class="room-container ">
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="service_date[]">Date</label>
                                                                            <select name="service_date[]" class="custom-select" required>
                                                                                <option selected value="{{ $service_date[$i] }}">{{ dateFormat($service_date[$i]) }}</option>
                                                                                @foreach ($date_stay as $datestay)
                                                                                    <option value="{{ date('Y-m-d',strtotime($datestay)) }}">{{ dateFormat($datestay) }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                           
                                                                            <label for="optional_rate_id[]">Services</label>
                                                                            <select name="optional_rate_id[]" class="custom-select" required>
                                                                                <option selected value="{{  $optional_service_name->id }}">{{ $optional_service_name->name }}</option>
                                                                                @foreach ($optional_services as $optional_service)
                                                                                    <option value="{{ $optional_service->id }}">{{ $optional_service->name }}</option>
                                                                                    
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <div class="form-group">
                                                                            <label for="number_of_guest[]">Number of Guest  </label>
                                                                            <input type="number" min="1" max="{{ $order->number_of_guests }}" name="number_of_guest[]" class="form-control m-0 @error('number_of_guest[]') is-invalid @enderror" placeholder="Max {{ $order->number_of_guests }}" value="{{ $oro_nog[$i] }}" required>
                                                                            @error('number_of_guest[]')
                                                                                <div class="alert alert-danger">
                                                                                    {{ $message }}
                                                                                </div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-1">
                                                                        <button class="btn btn-remove remove"  type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i> </button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </li>
                                                @endfor
                                            @endif
                                            <div class="after-add-more"></div> 
                                        </ol>
                                    </div>
                                    <div class="col-md-12 text-right">
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                        <button type="button" class="btn btn-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Service</button>
                                        <button type="submit" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                        <a href="/orders-admin-{{ $order->id }}#optional_service">
                                            <button type="button" class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                        </a>
                                    </div>
                                </div>
                            </form>
                            <div class="copy hide">
                                <li class="m-b-8">
                                    <div class="control-group">
                                        <div class="room-container ">
                                            <div class="row">
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="service_date[]">Date</label>
                                                        <select name="service_date[]" class="custom-select" required>
                                                            <option selected value="">Select Date</option>
                                                            @foreach ($date_stay as $datestay)
                                                                <option value="{{ date('Y-m-d',strtotime($datestay)) }}">{{ dateFormat($datestay) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-4">
                                                    <div class="form-group">
                                                        <label for="optional_rate_id[]">Services</label>
                                                        <select name="optional_rate_id[]" class="custom-select" required>
                                                            <option selected value="">Select Service</option>
                                                            @foreach ($optional_services as $optional_service)
                                                                <option value="{{ $optional_service->id }}">{{ $optional_service->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-sm-3">
                                                    <div class="form-group">
                                                        <label for="number_of_guest[]">Number of Guest  </label>
                                                        <input type="number" min="1" max="{{ $order->number_of_guests }}" name="number_of_guest[]" class="form-control m-0 @error('number_of_guest[]') is-invalid @enderror" placeholder="Max {{ $order->number_of_guests }}" value="" required>
                                                        @error('number_of_guest[]')
                                                            <div class="alert alert-danger">
                                                                {{ $message }}
                                                            </div>
                                                        @enderror
                                                    </div>
                                                </div>
                                                <div class="col-sm-1">
                                                    <button class="btn btn-remove remove"  type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i> </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </div>
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    var jmlh_room = 8;
                                    var ro = 1;
                                    $(".add-more").click(function(){ 
                                        if(ro < jmlh_room){ 
                                            ro++;
                                            var html = $(".copy").html();
                                            $(".after-add-more").before(html);
                                        }
                                    });
                                    $("body").on("click",".remove",function(){ 
                                        $(this).parents(".control-group").remove();
                                    });
                                });
                            </script>
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
                @include('layouts.footer')
            </div>
        </div>
    </div>
@endsection
