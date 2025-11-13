@section('title', __('messages.Tours'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12">
                        <div class="title"><i class="icon-copy fa fa-search"></i>&nbsp; @lang('messages.Search Tours')</div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard">@lang('messages.Dashboard')</a></li>
                                <li class="breadcrumb-item"><a href="tours">@lang('messages.Tours')</a></li>
                                <li class="breadcrumb-item active" aria-current="page">@lang('messages.Search Tours')</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            {{-- ALERT --}}
            @if (count($errors) > 0)
                @include('partials.alert', ['type' => 'danger', 'messages' => $errors->all()])
            @endif
            @if (\Session::has('success'))
                @include('partials.alert', ['type' => 'success', 'messages' => [\Session::get('success')]])
            @endif
            @if (\Session::has('danger'))
                @include('partials.alert', ['type' => 'danger', 'messages' => [\Session::get('danger')]])
            @endif
            @if ($bookingcode_status == "Invalid" || $bookingcode_status == "Expired" || $bookingcode_status == "Used")
                <div class="alert-error-code">
                    <div class="alert alert-danger">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            @if ($bookingcode_status == "Invalid")
                                <li><div ><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>@lang('messages.Invalid Code')</div></li>
                            @elseif ($bookingcode_status == "Expired")
                                <li><div ><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>@lang('messages.Expired Code')</div></li>
                            @elseif ($bookingcode_status == "Used")
                                <li><div ><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>@lang('messages.Used Code')</div></li>
                            @endif
                        </ul>
                    </div>
                </div>
            @endif
            <div class="row">
                {{-- BOOKING CODE AND PROMOTION =================================================================================================== --}}
                {{-- BOOKING CODE AND PROMOTION --}}
                @if ($bookingcode != "" || $promotions)
                    @include('partials.bookingcode-promotions-card', ['link' => '/search-hotels','method'=>'POST'])
                @endif
                <div class="col-md-12">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle"><i class="fa fa-suitcase"></i> @lang("messages.Tours")</div>
                        </div>
                        <form action="/search-tours" method="POST" role="search";>
                            {{ csrf_field() }}
                            <div class="search-container">
                                <div class="search-item">
                                    <select name="tour_location" value="{{ old('tour_location') }}" class="custom-select">
                                        @if (isset($tour_location))
                                            <option value="{{ $tour_location }}">{{ $tour_location }}</option>
                                            <option value="">@lang('messages.All Tours')</option>
                                        @else
                                            <option value="">@lang('messages.Search by location')</option>
                                        @endif
                                        @php $t_lc = ""; @endphp
                                        @foreach ($tours as $t_location)
                                            @if ($t_location->location !== $t_lc)
                                                <option value="{{ $t_location->location }}">{{ $t_location->location }}</option>
                                                @php $t_lc = $t_location->location; @endphp
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="search-item">
                                    <select id="tour_type" name="tour_type" value="{{ old('tour_type') }}" class="custom-select @error('tour_type') is-invalid @enderror">
                                        @if (isset($tour_type))
                                            <option value="{{ $tour_type }}">{{ $tour_type }}</option>
                                            <option value="">@lang('messages.All Tours')</option>
                                        @else
                                            <option value="">@lang('messages.Search by type')</option>
                                        @endif
                                        @php
                                            $t_ty = "";
                                        @endphp
                                        @foreach ($tours as $t_type)
                                            @if ($t_type->type !== $t_ty)
                                                <option value="{{ $t_type->type }}">{{ $t_type->type }}</option>
                                                @php
                                                    $t_ty = $t_type->type;
                                                @endphp
                                            @endif
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="alert alert-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="search-item">
                                    @if (isset($bookingcode->code))
                                        <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ $bookingcode->code }}">
                                    @else
                                        <input type="text" style="text-transform: uppercase;" class="form-control" name="bookingcode" placeholder="@lang('messages.Enter Booking Code')" value="{{ old('bookingcode') }}">
                                    @endif
                                </div>
                                <button type="submit" class="btn-search btn-primary"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Search')</button>
                            </div>
                        </form>
                        @if (count($tours) > 0)
                            <div class="info-container">
                                <div class="info-title">@lang('messages.Result'): <span>@lang('messages.Found') {{ count($tours) }} @lang('messages.Tours')</span></div>
                            </div>
                            <div class="card-box-content">
                                @foreach ($tours as $tour)
                                <div class="card">
                                    <div class="image-container">
                                        <div class="first">
                                            <div class="card-lable">
                                                <div class="meta-box">
                                                    <p class="text"><i class="icon-copy fa fa-map-marker" aria-hidden="true"></i> {{ $tour->location }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $hasBookingCode = isset($bookingcode->code);
                                            $hasPromotion = $promotion_price > 0;
                                            $priceCardText = $hasBookingCode ? __('messages.Code') : __('messages.Promo');
                                            $priceValue = $hasBookingCode ? $bookingcode->discounts : $promotion_price;
                                            $link = $hasBookingCode ? "/tour-$tour->code-$bookingcode->code" : "/tour-$tour->code";
                                        @endphp
                                        @if ($hasBookingCode || $hasPromotion)
                                            <div class="price-card m-t-8">
                                                {{ $priceCardText . " $ " . number_format($priceValue) }}
                                            </div>
                                        @endif
                                        <a href="{{ $link }}">
                                            <img src="{{ asset('storage/tours/tours-cover/' . $tour->cover) }}" class="img-fluid rounded thumbnail-image">
                                            <div class="card-detail-title">{{ $tour->name }}</div>
                                        </a>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <div class="pagination">
                                <div class="pagination-panel">
                                    {{ $tours->links() }}
                                </div>
                                <div class="pagination-desk">
                                    {{ $tours->total() }} <span>@lang('messages.Tours Available')</span>
                                </div>
                            </div>
                        @else
                            <div class="info-container">
                                <div class="info-title">@lang('messages.Result'): <span><i class="icon-copy fa fa-exclamation" aria-hidden="true"></i> @lang('messages.The tour you were looking for was not found, please try with other keywords')!</span></div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @include('layouts.footer')
        </div>
    </div>
@endsection
