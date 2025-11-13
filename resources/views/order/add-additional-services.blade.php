@section('title', __('messages.Additional Services'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title"><i class="icon-copy fa fa-tags"></i>&nbsp; Add Additional Services</div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="orders-admin">Orders Admin</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:history.back()">{{ $order->orderno }}</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Add Additional Services</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="title">
                                    Order Detail
                                </div>
                            </div>
                            <div class="col-md-12">
                                <table class="table tb-list">
                                    <tr>
                                        <td class="htd-1">Order No.</td>
                                        <td class="htd-2">{{ $order->orderno }}</td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">Order Date.</td>
                                        <td class="htd-2">{{ date('l, m/d/Y (H.i A)',strtotime($order->created_at)) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">Service</td>
                                        <td class="htd-2">{{ $order->service }}</td>
                                    </tr>
                                    @if ($order->service == "Wedding Package")
                                        
                                        <tr>
                                            <td class="htd-1">Wedding Date</td>
                                            <td class="htd-2">{{ date('l, m/d/Y (H.i A)',strtotime($order->wedding_date)) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td class="htd-1">Check In</td>
                                        <td class="htd-2">{{ date('l, m/d/Y',strtotime($order->checkin)) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">Check Out</td>
                                        <td class="htd-2">{{ date('l, m/d/Y',strtotime($order->checkout)) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="htd-1">Duration</td>
                                        @if ($order->service == "Wedding Package" or $order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package")
                                            @if ($order->duration > 1)
                                                <td class="htd-2">{{ $order->duration." nights" }}</td>
                                            @else
                                                <td class="htd-2">{{ $order->duration." night" }}</td>
                                            @endif
                                        @elseif ($order->service == "Private Villa")
                                            <td class="htd-2">{{ $order->duration." " }}{{ $order->duration > 1 ?"days":"day" }}</td>
                                        @elseif ($order->service == "Tour Package")
                                            @if ($order->duration == 1)
                                                <td class="htd-2">{{ $order->duration."D" }}</td>
                                            @elseif ($order->duration == 2)
                                                <td class="htd-2">{{ ($order->duration)."D/".($order->duration - 1)."N" }}</td>
                                            @elseif ($order->duration == 3)
                                                <td class="htd-2">{{ ($order->duration)."D/".($order->duration - 1)."N" }}</td>
                                            @elseif ($order->duration == 4)
                                                <td class="htd-2">{{ ($order->duration)."D/".($order->duration - 1)."N" }}</td>
                                            @elseif ($order->duration == 5)
                                                <td class="htd-2">{{ ($order->duration)."D/".($order->duration - 1)."N" }}</td>
                                            @elseif ($order->duration == 6)
                                                <td class="htd-2">{{ ($order->duration)."D/".($order->duration - 1)."N" }}</td>
                                            @elseif ($order->duration == 7)
                                                <td class="htd-2">{{ ($order->duration)."D/".($order->duration - 1)."N" }}</td>
                                            @endif
                                        @else
                                            @if ($order->duration > 1)
                                                <td class="htd-2">{{ $order->duration." hours" }}</td>
                                            @else
                                                <td class="htd-2">{{ $order->duration." hour" }}</td>
                                            @endif
                                        @endif
                                    </tr>
                                    <tr>
                                        <td class="htd-1">Number of Guests</td>
                                        <td class="htd-2">{{ $order->number_of_guests." guests" }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="title">
                                    Add Additional Services
                                </div>
                            </div>

                            <form id="edit-additional-service" action="/fedit-additional-services-{{ $order->id }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @method('put')
                                <div class="row">
                                    <div class="col-md-12">
                                        @if ($additional_service)
                                            @php
                                                $cas = count($additional_service);
                                            @endphp
                                            @for ($i = 0; $i < $cas; $i++)
                                                <div class="control-group">
                                                    <div class="row">
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="additional_service_date[{{ $i }}]">Date</label>
                                                                <input type="date" name="additional_service_date[{{ $i }}]" class="form-control @error('additional_service_date[{{ $i }}]') is-invalid @enderror" placeholder="Service" value="{{ $additional_service_date[$i] }}" required>
                                                                @error('additional_service_date[{{ $i }}]')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-4">
                                                            <div class="form-group">
                                                                <label for="additional_service[{{ $i }}]">Additional Service</label>
                                                                <input type="text" name="additional_service[{{ $i }}]" class="form-control @error('additional_service[{{ $i }}]') is-invalid @enderror" placeholder="Service" value="{{ $additional_service[$i] }}" required>
                                                                @error('additional_service[{{ $i }}]')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-2">
                                                            <div class="form-group">
                                                                <label for="additional_service_qty[{{ $i }}]">Quantity</label>
                                                                <input type="number" name="additional_service_qty[{{ $i }}]" min="1" class="form-control m-0 @error('additional_service_qty[{{ $i }}]') is-invalid @enderror" placeholder="Service" value="{{ $additional_service_qty[$i] }}" required>
                                                                @error('additional_service_qty[{{ $i }}]')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-3">
                                                            <div class="form-group">
                                                                <label for="additional_service_price[{{ $i }}]">Price /pax</label>
                                                                <input type="number" name="additional_service_price[{{ $i }}]" min="1" class="form-control @error('additional_service_price[{{ $i }}]') is-invalid @enderror" placeholder="Price in USD" value="{{ $additional_service_price[$i] }}" required>
                                                                @error('additional_service_price[{{ $i }}]')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-sm-1" style="align-self: center; padding-bottom:17px;">
                                                            <button class="btn btn-remove remove"  type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i> </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endfor
                                        @else
                                            <div class="control-group">
                                                <div class="row">
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="additional_service_date[]">Date</label>
                                                            <input type="date" name="additional_service_date[]" class="form-control @error('additional_service_date[]') is-invalid @enderror" placeholder="Service" value="" required>
                                                            @error('additional_service_date[]')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-4">
                                                        <div class="form-group">
                                                            <label for="additional_service[]">Additional Service</label>
                                                            <input type="text" name="additional_service[]" class="form-control @error('additional_service[]') is-invalid @enderror" placeholder="Service" value="" required>
                                                            @error('additional_service[]')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-2">
                                                        <div class="form-group">
                                                            <label for="additional_service_qty[]">Quantity</label>
                                                            <input type="number" name="additional_service_qty[]" min="1" class="form-control m-0 @error('additional_service_qty[]') is-invalid @enderror" placeholder="Service" value="" required>
                                                            @error('additional_service_qty[]')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3">
                                                        <div class="form-group">
                                                            <label for="additional_service_price[]">Price</label>
                                                            <input type="number" name="additional_service_price[]" min="1" class="form-control @error('additional_service_price[]') is-invalid @enderror" placeholder="Price in USD" value="" required>
                                                            @error('additional_service_price[]')
                                                                <div class="alert alert-danger">
                                                                    {{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="after-add-more"></div>
                                    <input type="hidden" name="order_id" value="{{ $order->id }}">
                                </div>
                            </form>
                            {{-- ADD MORE SERVICE --}}
                            <div class="copy hide">
                                <div class="col-md-12">
                                    <div class="control-group">
                                        <div class="row">
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="additional_service_date[]">Date</label>
                                                    <input type="date" name="additional_service_date[]" class="form-control @error('additional_service_date[]') is-invalid @enderror" placeholder="Service" value="" required>
                                                    @error('additional_service_date[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-4">
                                                <div class="form-group">
                                                    <label for="additional_service[]">Additional Service</label>
                                                    <input type="text" name="additional_service[]" class="form-control @error('additional_service[]') is-invalid @enderror" placeholder="Service" value="" required>
                                                    @error('additional_service[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-2">
                                                <div class="form-group">
                                                    <label for="additional_service_qty[]">Quantity</label>
                                                    <input type="number" name="additional_service_qty[]" min="1" class="form-control m-0 @error('additional_service_qty[]') is-invalid @enderror" placeholder="Service" value="" required>
                                                    @error('additional_service_qty[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-3">
                                                <div class="form-group">
                                                    <label for="additional_service_price[]">Price/pax</label>
                                                    <input type="number" name="additional_service_price[]" min="1" class="form-control @error('additional_service_price[]') is-invalid @enderror" placeholder="Price in USD" value="" required>
                                                    @error('additional_service_price[]')
                                                        <div class="alert alert-danger">
                                                            {{ $message }}
                                                        </div>
                                                    @enderror
                                                </div>
                                            </div>
                                            <div class="col-sm-1" style="align-self: center; padding-bottom:17px;">
                                                <button class="btn btn-remove remove"  type="button"><i class="icon-copy fa fa-close" aria-hidden="true"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <script type="text/javascript">
                                $(document).ready(function() {
                                    var ro = 1;
                                    $(".add-more").click(function(){ 
                                        ro++;
                                        var html = $(".copy").html();
                                        $(".after-add-more").before(html);
                                    });
                                    $("body").on("click",".remove",function(){ 
                                        $(this).parents(".control-group").remove();
                                    });
                                });
                            </script>
                            <div class="card-box-footer">
                                <button type="button" class="btn btn-primary add-more"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Service</button>
                                <button type="submit" form="edit-additional-service" class="btn btn-primary"><i class="fa fa-floppy-o" aria-hidden="true"></i> Save</button>
                                <a href="/orders-admin-{{ $order->id }}#additionalServices">
                                    <button type="button" class="btn btn-danger"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>
@endsection