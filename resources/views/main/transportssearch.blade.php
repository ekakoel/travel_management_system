@section('title', __('messages.Transports'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            @if (count($errors) > 0)
                <div class="alert-error-code">
                    <div class="alert alert-danger w3-animate-top">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li><div><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>{{ $error }}</div></li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
            @if (\Session::has('success'))
                <div class="alert-error-code">
                    <div class="alert alert-success w3-animate-top">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            <li><div><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>{!! \Session::get('success') !!}</div></li>
                        </ul>
                    </div>
                </div>
            @endif
            @if ($bookingcode_status == "Invalid")
                <div class="alert-error-code w3-animate-top">
                    <div class="alert alert-danger">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            <li><div ><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>@lang('messages.Invalid Code')</div></li>
                        </ul>
                    </div>
                </div>
            @endif
            @if ($bookingcode_status == "Expired")
                <div class="alert-error-code w3-animate-top">
                    <div class="alert alert-danger">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            <li><div ><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>@lang('messages.Expired Code')</div></li>
                        </ul>
                    </div>
                </div>
            @endif
            @if ($bookingcode_status == "Used")
                <div class="alert-error-code w3-animate-top">
                    <div class="alert alert-danger">
                        <span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span> 
                        <ul>
                            <li><div ><i class="alert-icon-code fa fa-exclamation-circle" aria-hidden="true"></i>@lang('messages.Used Code')</div></li>
                        </ul>
                    </div>
                </div>
            @endif
            {{-- CARD BOOKINGCODE AND PROMOTIONS =================================================================================================== --}}
            <div class="row" id="bookingcode_promotion">
                <div class="col-12 promotion-bookingcode">
                    @if ($bookingcode_status == "Valid")
                        <div class="bookingcode-card">
                            <div class="icon-card bookingcode">
                                <i class="fa fa-calendar-check-o" aria-hidden="true"></i>
                            </div>
                            <div class="content-card">
                                <div class="code">{{ $bookingcode->code }}</div>
                                <div class="text-card">@lang('messages.Booking Code') @lang('messages.Aplied')</div>
                                <div class="text-card">@lang('messages.Expired') {{ date('d M y', strtotime($bookingcode->expired_date)) }}</div>
                            </div>
                            <div class="content-card-price">
                                <div class="price"><span>$</span>{{ $bookingcode->discounts }}</div>
                                <form action="/search-transports" method="POST" role="search";>
                                    {{ csrf_field() }}
                                    <button type="submit" class="btn-remove-code"><i class="fa fa-close" aria-hidden="true"></i></button>
                                </form>
                            </div>
                        </div>
                        @if (isset($promotions))
                            @foreach ($promotions as $promotion)
                                <div class="bookingcode-card">
                                    <div class="icon-card promotion">
                                        <i class="fa fa-bullhorn" aria-hidden="true"></i>
                                    </div>
                                    <div class="content-card">
                                        <div class="code">{{ $promotion->name }}</div>
                                        <div class="text-card">@lang('messages.Promo Period')</div>
                                        <div class="text-card">{{ date('d M y', strtotime($promotion->periode_start))." - ".date('d M y', strtotime($promotion->periode_end)) }}</div>
                                    </div>
                                    <div class="content-card-promo">
                                        <div class="price"><span>$</span>{{ $promotion->discounts }}</div>
                                        <button class="btn-remove-code" data-toggle="tooltip" data-placement="top" title='@lang('messages.Ongoing promotion'){{" ". $promotion->name." "}}@lang('messages.and get discounts'){{ " $".$promotion->discounts." " }}@lang('messages.until'){{ " ". date('d M y',strtotime($promotion->periode_end)) }}'><i class="fa fa-question" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @else
                        @if (isset($promotions))
                            @foreach ($promotions as $promotion)
                                <div class="bookingcode-card">
                                    <div class="icon-card promotion">
                                        <i class="fa fa-bullhorn" aria-hidden="true"></i>
                                    </div>
                                    <div class="content-card">
                                        <div class="code">{{ $promotion->name }}</div>
                                        <div class="text-card">@lang('messages.Promo Period')</div>
                                        <div class="text-card">{{ date('d M y', strtotime($promotion->periode_start))." - ".date('d M y', strtotime($promotion->periode_end)) }}</div>
                                    </div>
                                    <div class="content-card-promo">
                                        <div class="price"><span>$</span>{{ $promotion->discounts }}</div>
                                        <button class="btn-remove-code" data-toggle="tooltip" data-placement="top" title='@lang('messages.Ongoing promotion'){{" ". $promotion->name." "}}@lang('messages.and get discounts'){{ " $".$promotion->discounts." " }}@lang('messages.until'){{ " ". date('d M y',strtotime($promotion->periode_end)) }}'><i class="fa fa-question" aria-hidden="true"></i></button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @endif
                </div>
            </div>
            {{-- END BOOKING CODE AND PROMOTION =================================================================================================== --}}
            <div class="card-box p-b-0 mb-30">
                <div class="card-box-title">
                    <div class="subtitle"><i class="icon-copy fa fa-car" aria-hidden="true"></i> @lang("messages.Transport")</div>
                </div>
                <form action="/search-transports" method="POST" role="search";>
                    {{ csrf_field() }}
                    <div class="search-container">
                        <div class="search-item">
                            <input type="text" class="form-control" name="brand" placeholder="@lang('messages.Search by brand')" value="{{ old('brand') }}">
                        </div>
                        <div class="search-item">
                            <select name="type" value="{{ old('type') }}" class="custom-select col-12 @error('type') is-invalid @enderror">
                                <option selected value="">@lang('messages.Search by Type')</option>
                                @foreach ($type as $type)
                                    <option value="{{ $type->type }}">@lang('messages.'.$type->type)</option>
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
                        
                @if (count($transports_result) > 0)
                    <div class="info-title">@lang('messages.Result'): <span>@lang('messages.Found') {{ count($transports_result) }} @lang('messages.Transport')</span></div>
                @else
                    <div class="info-title">@lang('messages.Result'): <span><i class="icon-copy fa fa-exclamation" aria-hidden="true"></i> @lang('messages.The transport you were looking for was not found, please try with other keywords')!</span></div>
                @endif

                <div class="card-box-content">
                    @foreach ($transports_result as $transports)
                        <div class="card">
                            <div class="image-container">
                                <div class="first">
                                    <ul class="card-lable">
                                        <li class="item">
                                            <div class="meta-box">
                                                <i class="icon-copy ion-ios-person"></i> {{ $transports->capacity }} @lang('messages.Seat')
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                                @if (isset($bookingcode->code) or $promotion_price>0)
                                    @if (isset($bookingcode->code))
                                        <div class="price-card m-t-8">
                                            @lang('messages.Code'){{" $" . number_format($bookingcode->discounts) }}
                                        </div>
                                    @endif
                                    @if ($promotion_price>0)
                                        <div class="price-card m-t-27">
                                            @lang('messages.Promo'){{" $" . number_format($promotion_price) }}
                                        </div>
                                    @endif
                                @endif
                                <a href="/transport-{{ $transports->code }}{{ isset($bookingcode->code) ? '-'.$bookingcode->code:""; }}">
                                    <img class="obj-fit" src="{{ asset('storage/transports/transports-cover/' . $transports->cover) }}" class="img-fluid rounded thumbnail-image">
                                    <div class="card-detail-title">{{ $transports->name }}</div>
                                </a>
                            </div>
                        </div>
                    @endforeach    
                </div>
                    {{-- <div class="d-flex" style="padding: 1% 3% 2% 2%">
                        {!! $transports_result->links() !!}
                    </div> --}}
            </div>
            @include('layouts.footer')
        </div>
    </div>
@endsection
