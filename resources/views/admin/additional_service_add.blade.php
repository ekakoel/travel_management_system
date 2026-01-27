@php
    $from = $order->checkin;
    $dur = $order->duration + 1;
    $date_stay = [];
    for ($a=0; $a < $dur ; $a++) { 
        $date_stay[$a] = $from;
        $from = date('Y-m-d',strtotime('+1 days',strtotime($from)));
    }
@endphp
@section('title', __('messages.Add Additional Service'))
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
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="fa fa-asterisk"></i>@lang('messages.Additional Services')</div>
                            </div>
                            <table class="data-table table nowrap">
                                <thead>
                                    <tr>
                                        <th>@lang('messages.No')</th>
                                        <th>@lang('messages.Type')</th>
                                        <th>@lang('messages.Service')</th>
                                        <th>@lang('messages.Description')</th>
                                        <th>@lang('messages.Price')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($optional_services as $optional_service)
                                        <tr>
                                            <td>
                                                <div class="table-service-name">{{ $loop->iteration }}</div>
                                            </td>
                                            <td>
                                                <div class="table-service-name">{{ $optional_service->type }}</div>
                                            </td>
                                            <td>
                                                <div class="table-service-name">{{ $optional_service->name }}</div>
                                            </td>
                                            <td>
                                                <div class="table-service-name">{!! $optional_service->description !!}</div>
                                            </td>
                                            <td>
                                                <div class="table-service-name">{{ currencyFormatUsd($optional_service->calculatePrice($usdrates, $tax))." /" }}@lang('messages.pax')</div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 desktop">
                        <div class="row">
                            @include('admin.usd-rate')
                            @include('layouts.attentions')
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="subtitle"><i class="fa fa-pencil"></i>@lang('messages.Edit Additional Charge')</div>
                            </div>
                            @if(count($optional_rate_orders)>0)
                                <form id="updateOrderAdditionalCharge" action="{{ route('func.admin-update-optional-service-order',$order->id) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @method('put')
                            @else
                                <form id="updateOrderAdditionalCharge" action="{{ route('func.admin-add-optional-service-order',$order->id) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                            @endif
                                <div class="row">
                                    <div class="col-md-12">
                                            <ol>
                                                @if(count($optional_rate_orders)>0)
                                                    @foreach ($optional_rate_orders as $optional_rate_order)
                                                        <ul class="m-b-8 room-container">
                                                            <div class="control-group">
                                                                <div class="row">
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="service_date[]">@lang('messages.Date')</label>
                                                                            <select name="service_date[]" class="custom-select" required>
                                                                                @if ($optional_rate_order->mandatory == 1)
                                                                                    <option selected value="{{ $optional_rate_order->service_date }}">{{ dateFormat($optional_rate_order->service_date) }}</option>
                                                                                @else
                                                                                    @foreach ($date_stay as $datestay)
                                                                                        <option {{ $optional_rate_order->service_date === $datestay ? "selected" : ""; }} value="{{ date('Y-m-d',strtotime($datestay)) }}">{{ dateFormat($datestay) }}</option>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-4">
                                                                        <div class="form-group">
                                                                            <label for="optional_rate_id[]">@lang('messages.Services')</label>
                                                                            <select name="optional_rate_id[]" class="custom-select" required>
                                                                                @if ($optional_rate_order->mandatory == 1)
                                                                                    <option selected value="{{ $optional_rate_order->optional_rate_id }}">{{ $optional_rate_order->optional_rate->name }}</option>
                                                                                @else
                                                                                    @foreach ($optional_services as $optional_service)
                                                                                        <option {{ $optional_rate_order->optional_rate->id === $optional_service->id ? "selected" : ""; }} value="{{ $optional_service->id }}">{{ $optional_service->name }}</option>
                                                                                    @endforeach
                                                                                @endif
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-sm-3">
                                                                        <div class="form-group">
                                                                            <label for="number_of_guest[]">@lang('messages.Number of Guest')</label>
                                                                            <input {{ $optional_rate_order->mandatory == 1?"readonly":""; }} type="number" min="1" max="{{ $order->number_of_guests }}" name="number_of_guest[]" class="form-control m-0 @error('number_of_guest[]') is-invalid @enderror" placeholder="Max {{ $order->number_of_guests }}" value="{{ $optional_rate_order->number_of_guest }}" required>
                                                                            @error('number_of_guest[]')
                                                                                <div class="alert alert-danger">
                                                                                    {{ $message }}
                                                                                </div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <input type="hidden" name="optional_rate_order_id[]" value="{{ $optional_rate_order->id }}">
                                                            @if ($optional_rate_order->mandatory != 1)
                                                                <button class="btn btn-remove btnremove remove" data-id="{{ $optional_rate_order->id }}"><i class="icon-copy fa fa-close" aria-hidden="true"></i></button>
                                                            @endif
                                                        </ul>
                                                    @endforeach
                                                @else
                                                    <p class="p-b-18"><i>@lang('messages.Your order will be displayed here!')</i></p>
                                                @endif
                                                <div class="after-add-more"></div>
                                            </ol>
                                        </div>
                                        <input type="hidden" name="status" value="Draft">
                                    </div>
                                </form>
                        
                                <div class="card-box-footer">
                                    <button type="button" class="btn btn-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> @lang('messages.Add')</button>
                                    <button type="submit" form="updateOrderAdditionalCharge" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> @lang('messages.Save')</button>
                                    <a href="{{ route('view.detail-order-admin',$order->id) }}#optional_service">
                                        <button type="button" class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                    </a>
                                </div>
                            
                    
                            <div class="copy hide">
                                <ul class="m-b-8 room-container">
                                    <div class="control-group">
                                        <div class="row">
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="service_date[]">@lang('messages.Date')</label>
                                                    <select name="service_date[]" class="custom-select" required>
                                                        <option selected value="">@lang('messages.Select date')</option>
                                                        @foreach ($date_stay as $datestay)
                                                            <option value="{{ date('Y-m-d',strtotime($datestay)) }}">{{ dateFormat($datestay) }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="optional_rate_id[]">@lang('messages.Services')</label>
                                                    <select name="optional_rate_id[]" class="custom-select" required>
                                                        <option selected value="">@lang('messages.Select service')</option>
                                                        @foreach ($optional_services as $additional_charge)
                                                            <option value="{{ $additional_charge->id }}">{{ $additional_charge->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="number_of_guest[]">@lang('messages.Number of Guest')</label>
                                                    <input type="number" min="1" max="{{ $order->number_of_guests }}" name="number_of_guest[]" class="form-control m-0 @error('number_of_guest[]') is-invalid @enderror" placeholder="Max {{ $order->number_of_guests }}" value="" required>
                                                    @error('number_of_guest[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="optional_rate_order_id[]" value=0>
                                    <button class="btn btn-remove remove" type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i></button>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                $(this).parents(".room-container").remove();
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // Delete via AJAX
            $("body").on("click", ".btnremove", function(e) {
                e.preventDefault();

                var button = $(this);
                var id = button.data("id");

                if (!confirm("Are you sure you want to delete this item?")) return;

                $.ajax({
                    url: "/optional-rate-order/" + id,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status === "success") {
                            button.closest(".room-container").remove();
                        } else {
                            alert(response.message);
                        }
                    },
                    error: function(xhr) {
                        console.error("Delete failed:", xhr);
                        alert("Failed to delete. Please try again.");
                    }
                });
            });
        });
    </script>

@endsection
