@section('title', __('messages.Tour'))
@section('content')
    @extends('layouts.head')
    @php
        $imagePath = public_path('/storage/tours/tours-cover/'. $tour->cover);
    @endphp
	<div class="mobile-menu-overlay"></div>
	<div class="main-container">
		<div class="pd-ltr-20">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title"><i class="icon-copy dw dw-map-2"></i>&nbsp; @lang('messages.Tours')</div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">@lang('messages.Dashboard')</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('view.tours') }}">@lang('messages.Tours')</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $tour->$langName }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
			@if (count($errors) > 0)
                <div class="alert-error-code">
                    <div class="alert alert-danger">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li><div><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>{{ $error }}</div></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            @if (\Session::has('danger'))
                <div class="alert-error-code">
                    <div class="alert alert-danger">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            <li><div><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>{!! \Session::get('danger') !!}</div></li>
                        </ul>
                    </div>
                </div>
            @endif
            @if (\Session::has('success'))
                <div class="alert-error-code">
                    <div class="alert alert-success">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            <li><div><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>{!! \Session::get('success') !!}</div></li>
                        </ul>
                    </div>
                </div>
            @endif
            <div class="row">
                <div class="col-md-4 m-b-18 mobile">
                    {{-- ATTENTIONS --}}
                    <div class="row">
                        @include('layouts.attentions')
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card-box m-b-18">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="fa fa-file-text"></i>{{ $tour->$langName }}</div>
                            <span class="m-t-8">{{ $tour->type?->$langType }}</span>
                        </div>
                        <div class="card-box-body">
                            <div class="cover-image m-b-18">
                                @if (!empty($tour->cover) && file_exists($imagePath))
                                    <img src="/storage/tours/tours-cover/{{ $tour->cover }}" alt="{{ $tour->langName }}">
                                @else
                                    <img src="{{ getThumbnail('/images/default.webp', 380, 200) }}" alt="{{ $tour->langName }}">
                                @endif
                            </div>
                        </div>
                        <div class="page-card">
                            <div class="card-content">
                                <div class="m-b-18">
                                    <div class="card-subtitle">{{ $tour->$langName }}</div>
                                    <p>
                                        {!! $tour->$langShortDescription !!}
                                    </p>
                                </div>
                                <div class="card-subtitle">@lang('messages.Itinerary')</div>
                                <div class="m-b-18">
                                    <p>
                                        {!! $tour->$langItinerary !!}
                                    </p>
                                </div>
                                <div class="card-subtitle">@lang('messages.Inclusions')</div>
                                <div class="m-b-18">
                                    <p>
                                        {!! $tour->$langInclude !!}
                                    </p>
                                </div>
                                <div class="card-subtitle">@lang('messages.Exclusions')</div>
                                <div class="m-b-18">
                                    <p>
                                        {!! $tour->$langExclude !!}
                                    </p>
                                </div>
                                @if ($tour->additional_info)
                                    <div class="card-subtitle">@lang('messages.Additional Information')</div>
                                    <div class="m-b-18">
                                        <p>
                                            {!! $tour->$langAdditionalInfo !!}
                                        </p>
                                    </div>
                                @endif
                                @if ($tour->cancellation_policy)
                                    <div class="card-subtitle">@lang('messages.Cancellation Policy')</div>
                                    <div class="m-b-18">
                                        <p>
                                            {!! $tour->$langCancellationPolicy !!}
                                        </p>
                                    </div>
                                @endif
                                @if (count($tour->images)>0)
                                    <div class="card-subtitle">@lang('messages.Tour Gallery')</div>
                                    <div class="modal-galery">
                                        @foreach ($tour->images as $tour_image)
                                            <a href="#" data-toggle="modal" data-target="#gallery-{{ $tour_image->id }}">
                                                <div class="gallery-item" id="image-{{ $tour_image->id }}">
                                                    <img src="{{ getThumbnail("/tours/tour-gallery/".$tour_image->image,380,200) }}" class="thumbnail-image" loading="lazy">
                                                </div>
                                            </a>
                                            {{-- MODAL Images DETAIL --}}
                                            <div class="modal fade" id="gallery-{{ $tour_image->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="subtitle"><i class="icon-copy fa fa-image" aria-hidden="true"></i>{{ $tour->$langName }}</div>
                                                            </div>
                                                            <div class="card-box-body">
                                                                <div class="row">
                                                                    <div class="col-md-12">
                                                                        <div class="page-card">
                                                                            <div class="modal-image">
                                                                                <img src="{{ asset ('storage/tours/tour-gallery/' . $tour_image->image) }}" alt="{{ $tour->name }}" loading="lazy">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-box-footer">
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Close')</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                        @if (count($tour->prices))
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row" style="justify-content: flex-end;">
                                        <div class="col-12">
                                            <div class="card-box-subtitle m-t-0">
                                                <b>@lang('messages.Price')</b>
                                            </div>
                                            <table class="table stripe ">
                                                <tr>
                                                    <td class="width:5%;"><b>@lang('messages.No')</b></td>
                                                    <td class="width:15%;"><b>@lang('messages.Number of Guests')</b></td>
                                                    <td class="width:80%;"><b>@lang('messages.Price')/@lang('messages.pax')</b></td>
                                                </tr>
                                                @foreach ($tour->prices as $pr_no=>$tour_price)
                                                    <tr>
                                                        <td>{{ ++$pr_no }}</td>
                                                        <td>{{ $tour_price->min_qty." - ".$tour_price->max_qty }} @lang('messages.guests')</td>
                                                        <td>{{ currencyFormatUsd($tour_price->calculated_price) }}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-box-footer">
                                <a href="#" data-toggle="modal" data-target="#order-tour-{{ $tour->id }}">
                                    <button class="btn btn-primary">@lang('messages.Book Now')</button>
                                </a>
                            </div>
                            {{-- Modal Order Tour Package --------------------------------------------------------------------------------------------------------------- --}}
                            <div class="modal fade" id="order-tour-{{ $tour->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="card-box">
                                            <div class="card-box-title">
                                                <div class="subtitle"><i class="fa fa-tags"></i>@lang('messages.Create Order')</div>
                                            </div>
                                        
                                            <div class="row">
                                                <div class="col-6 col-md-6">
                                                    <div class="order-bil text-left">
                                                        <img src="{{ asset(config('app.logo_dark')) }}" alt="{{ config('app.alt_logo') }}">
                                                    </div>
                                                </div>
                                                <div class="col-6 col-md-6 flex-end">
                                                    <div class="label-title">@lang('messages.Order')</div>
                                                </div>
                                                <div class="col-md-12 text-right">
                                                    <div class="label-date float-right" style="width: 100%">
                                                        {{ dateFormat($now) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <form id="add-order-{{ $tour_price->id }}" action="{{ route('func.order-tour-package.create',$tour->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-body pd-5">
                                                    <div class="business-name">{{ $business->name }}</div>
                                                    <div class="bussines-sub">{{ __('messages.'.$business->caption) }}</div>
                                                    <hr class="form-hr">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="page-list">
                                                                        @lang('messages.Order No')
                                                                    </div>
                                                                    <div class="page-list">
                                                                        @lang('messages.Order Date')
                                                                    </div>
                                                                    <div class="page-list">
                                                                        @lang('messages.Service')
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="page-list-value">
                                                                        {{ 'ORD.' . date('Ymd', strtotime($now)) . '.TP' . $ordernotours }}
                                                                    </div>
                                                                    <div class="page-list-value">
                                                                        {{ dateFormat($now) }}
                                                                    </div>
                                                                    <div class="page-list-value">
                                                                        @lang('messages.Tour Package')
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- Admin create order ================================================================= --}}
                                                        @canany(['posDev','posAuthor','posRsv'])
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="user_id">Select Agent <span>*</span></label>
                                                                    <select name="user_id" class="custom-select @error('user_id') is-invalid @enderror" value="{{ old('user_id') }}" required>
                                                                        <option selected value="">Select Agent</option>
                                                                        @foreach ($agents as $agent)
                                                                            <option class="option-list" value="{{ $agent->id }}">{{ $agent->name." (".$agent->code.") @".$agent->office }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    @error('user_id')
                                                                        <div class="alert-form">{{ $message }}</div>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        @endcan
                                                        {{-- Admin create order ================================================================= --}}
                                                    </div>
                                                    <div class="page-subtitle">@lang('messages.Tour Package')</div>
                                                    <div class="content-body">
                                                        <div class="c-b-content">
                                                            <div class="c-b-c-title">@lang('messages.Tour Package')</div>
                                                            <div class="c-b-c-text">{{ $tour->name }}</div>
                                                        </div>
                                                        <div class="c-b-content">
                                                            <div class="c-b-c-title">@lang('messages.Type')</div>
                                                            <div class="c-b-c-text">{{ $tour->type?->$langType }}</div>
                                                        </div>
                                                        <div class="c-b-content">
                                                            <div class="c-b-c-title">@lang('messages.Duration')</div>
                                                            <div class="c-b-c-text">{{ $tour->duration_days ? $tour->duration_days."D": ""; }} {{ $tour->duration_nights ? "/ ".$tour->duration_nights."N": ""; }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="page-subtitle">@lang('messages.Guest Detail')</div>
                                                    <div class="row m-b-18">
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="travel_date">@lang('messages.Travel Date') </label>
                                                                <input name="travel_date" class="form-control @error('travel_date') is-invalid @enderror" placeholder="@lang('messages.Select date and time')" type="text" required>
                                                                @error('travel_date')
                                                                    <span class="invalid-feedback">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="nog">@lang('messages.Number of Guests') </label>
                                                                <input type="number" id="nog" onchange="calculate()"  min="2" name="number_of_guests" class="form-control @error('number_of_guests') is-invalid @enderror" placeholder="@lang('messages.Number of Guests')" value="{{ old('number_of_guests') }}" required>
                                                                @error('number_of_guests')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                       
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="pickup_location">@lang('messages.Pick up location') </label>
                                                                <input type="text" id="pickup_location" name="pickup_location" class="form-control @error('pickup_location') is-invalid @enderror" placeholder="@lang('messages.ex'): @lang('messages.Hotel Name')/@lang('messages.Airport')" value="{{ old('pickup_location') }}" required>
                                                                @error('pickup_location')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="form-group">
                                                                <label for="dropoff_location">@lang('messages.Drop off location') </label>
                                                                <input type="text" id="dropoff_location" name="dropoff_location" class="form-control @error('dropoff_location') is-invalid @enderror" placeholder="@lang('messages.ex'): @lang('messages.Hotel Name')/@lang('messages.Airport')" value="{{ old('dropoff_location') }}" required>
                                                                @error('dropoff_location')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="guest_detail">@lang('messages.Guest Detail') <span> *</span></label>
                                                                <textarea id="guest_detail" name="guest_detail" placeholder="ex: Mr. name, Mrs. name" class="textarea_editor form-control" value="{{ old('guest_detail') }}" required></textarea>
                                                                @error('guest_detail')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                        
                                                        <div class="col-md-12">
                                                            <div class="form-group">
                                                                <label for="note">@lang('messages.Note')</label>
                                                                <textarea id="note" name="note" placeholder="@lang('messages.Optional')" class="textarea_editor form-control" value="{{ old('note') }}"></textarea>
                                                                @error('note')
                                                                    <div class="alert alert-danger">
                                                                        {{ $message }}
                                                                    </div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-12 m-b-8">
                                                            <div class="box-price-kicked">
                                                                <div class="row">
                                                                    <div class="col-6 col-md-6">
                                                                        <div class="normal-text-07">@lang('messages.Price')/@lang('messages.pax')</div>
                                                                        <div class="normal-text-07">@lang('messages.Number of Guests')</div>
                                                                        <hr class="form-hr">
                                                                        <div class="price-name">@lang('messages.Total Price')</div>
                                                                    </div>
                                                                    <div class="col-6 col-md-6 text-right">
                                                                        <div class="normal-text-07"><span id="tour_price_per_pax"></span></div>
                                                                        <div class="normal-text"><span id="numberOfGuests">0</span></div>
                                                                        <hr class="form-hr">
                                                                        <div class="price-name-price"><span id="tour_final_price"></span></div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="notif-modal text-left">
                                                    @lang('messages.Please make sure all the data is correct before you submit an order!')
                                                </div>
                                            </Form>
                                            <div class="card-box-footer">
                                                <button type="submit" form="add-order-{{ $tour_price->id }}" id="normal-reserve" class="btn btn-primary"><i class="fa fa-shopping-basket" aria-hidden="true"></i> @lang('messages.Order')</button>
                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> @lang('messages.Cancel')</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
                <div class="col-md-4 desktop">
                    {{-- ATTENTIONS --}}
                    <div class="row">
                        @include('layouts.attentions')
                    </div>
                </div>
                
            </div>
            {{-- SIMILAR TOUR PACKAGE --}}
            @if (count($neartours) > 0)
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="icon-copy fa fa-map-signs" aria-hidden="true"></i>@lang('messages.Similar Tour Package')</div>
                    </div>
                    <div class="card-box-content">
                        @foreach ($neartours as $near_tour)
                            <div class="card">
                                <a href="tour-{{ $near_tour->slug }}">
                                    <div class="image-container">
                                        <div class="first">
                                            <ul class="card-lable">
                                                <li class="item">
                                                    <div class="meta-box">
                                                        <p class="text">{{ $near_tour->type?->$langType }}</p>
                                                    </div>
                                                </li>
                                            </ul>
                                        </div>
                                        <a href="/tour/{{ $near_tour->slug }}">
                                            @if (!empty($near_tour->cover) && file_exists($imagePath))
                                                <img src="/storage/tours/tours-cover/{{ $near_tour->cover }}" alt="{{ $near_tour->langName }}">
                                            @else
                                                <img src="{{ getThumbnail('/images/default.webp', 380, 200) }}" alt="{{ $near_tour->langName }}">
                                            @endif
                                        </a>
                                        <a href="/tour/{{ $near_tour->slug }}">
                                            <div class="card-detail-title">{{ $near_tour->$langName }}</div>
                                        </a>
                                        
                                    </div>
                                </a>
                            </div>
                            
                        @endforeach
                    </div>
                </div>
            @endif
			@include('layouts.footer')
		</div>
	</div>
    <script>
        const prices = @json($prices);
        function calculate() {
            const nogInput = document.getElementById('nog');
            const nog = parseInt(nogInput.value) || 0;

            if (nog === null) {
                document.getElementById('tour_price_per_pax').innerText = '-';
                document.getElementById('numberOfGuests').innerText = '-';
                document.getElementById('tour_final_price').innerText = '-';
                return;
            }

            // cari range harga
            let selected = null;
            for (const p of prices) {
                if (nog >= p.min_qty && nog <= p.max_qty) {
                    selected = p;
                    break;
                }
            }
            if (!selected) selected = prices[prices.length - 1];

            // pastikan nilai numerik
            const rate = parseFloat(selected.contract_rate ?? selected.price ?? 0);
            const markup = parseFloat(selected.markup ?? 0);
            const pricePerPax = rate + markup;

            if (isNaN(pricePerPax)) {
                console.error('⚠️ pricePerPax is NaN, data from backend may be invalid:', selected);
                return;
            }

            let total = pricePerPax * nog;
            let noofgu = nog;
            if (total < 0) total = 0;

            // tampilkan ke view
            document.getElementById('tour_price_per_pax').innerText = `${pricePerPax.toLocaleString()}`;
            document.getElementById('numberOfGuests').innerText = `${noofgu.toLocaleString()}`;
            document.getElementById('tour_final_price').innerText = `${total.toLocaleString()}`;

            // debug (opsional)
            console.log({
                nog, rate, markup, pricePerPax, total, selected
            });
        }
    </script>


@endsection
    
{{-- <script>
    function calculate(){
        var nog = document.getElementById('nog').value;
        var bookingcode_disc = @json($bookingcode_disc);
        var promotion_disc = @json($promotion_price);
        // var promotion_disc = document.getElementById('promo_disc').value;
        var cprice = document.getElementById('count_tour_prices').value;
        var price_pax_0 = document.getElementById('tour_price_pax_0').value;
        var price_pax_1 = document.getElementById('tour_price_pax_1').value;
        var price_pax_2 = document.getElementById('tour_price_pax_2').value;
        var qty_0 = document.getElementById('qty_0').value;
        var qty_1 = document.getElementById('qty_1').value;
        var qty_2 = document.getElementById('qty_2').value;
        var tp = 0;
        var ppp = 0;
        var t_price = 0;
        var normal_price = 0;

        if (nog < 5 && qty_0 < 5) {
            tp = (nog * price_pax_0);
            ppp = price_pax_0;
        } else if(nog < 10 && qty_0 < 10) {
            tp = (nog * price_pax_1);
            ppp = price_pax_1;
        } else if(nog < 17 && qty_0 < 17) {
            tp = (nog * price_pax_2);
            ppp = price_pax_2;
        } else{
            tp = (nog * price_pax_2);
            ppp = price_pax_2;
        } 

        if (bookingcode_disc > 0) {
            t_price = tp - bookingcode_disc;
            normal_price = tp;
        }else{
            t_price = tp;
            normal_price = tp;
        }

        if (promotion_disc > 0) {
            tot_price = t_price - promotion_disc;
            normal_price = tp;
        }else{
            tot_price = t_price;
            normal_price = tp;
        }

        var normalprice = normal_price.toLocaleString('en-US');
        var total_price = tot_price.toLocaleString('en-US');
       
        document.getElementById("tour_price_per_pax").innerHTML = ppp;
        document.getElementById("price_per_pax").value = ppp;
        document.getElementById("tour_final_price").innerHTML = total_price;
        document.getElementById("htour_final_price").value = total_price;
        document.getElementById("tour-normal-price").innerHTML = normalprice;
        document.getElementById("val-tour-normal-price").value = normalprice;
    }
</script> --}}