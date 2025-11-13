@section('title', __('messages.Activities'))
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
                                <form action="/search-activities" method="POST" role="search";>
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
                    <div class="subtitle"><i class="fa fa-child" aria-hidden="true"></i> @lang("messages.Activities")</div>
                </div>
                <form action="/search-activities" method="POST" role="search";>
                    {{ csrf_field() }}
                    <div class="search-container">
                        <div class="search-item">
                            <input type="text" class="form-control" name="location" placeholder="@lang('messages.Search by location')" value="{{ old('location') }}">
                        </div>
                        <div class="search-item">
                            <select id="activities_type" name="activities_type" value="{{ old('activities_type') }}" class="custom-select @error('activities_type') is-invalid @enderror">
                                <option value="">@lang('messages.Search by type')</option>
                                @foreach ($type as $type)
                                    <option value="{{ $type->type }}">@lang('messages.'. $type->type)</option>
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
                @if (count($activities) > 0)
                    <div class="info-container m-b-18">
                        <div class="info-title">@lang('messages.Result'): <span>@lang('messages.Found') {{ count($activities) }} @lang('messages.Activities')</span></div>
                    </div>
                @else
                    <div class="info-container m-b-18">
                        <div class="info-title">@lang('messages.Result'): <span><i class="icon-copy fa fa-exclamation" aria-hidden="true"></i>@lang('messages.The activity you were looking for was not found, please try with other keywords')!</span></div>
                    </div>
                @endif
                @if (count($activities) > 0)
                    <div class="card-box-content">
                        @foreach ($activities as $activity)
                            @php
                                $usrate = ceil($activity->contract_rate / $usdrates->rate);
                                $price_non_tax = $usrate + $activity->markup;
                                $tax = ceil($price_non_tax * ($taxes->tax / 100) );
                                if (isset($bookingcode) or isset($promotions)) {
                                    if (isset($bookingcode)) {
                                        $normal_price = $price_non_tax + $tax;
                                        if (isset($promotions)) {
                                            $final_price = ($price_non_tax + $tax)-$bookingcode->discounts - $promotion_price;
                                        }else{
                                            $final_price = ($price_non_tax + $tax)-$bookingcode->discounts;
                                        }
                                    }else{
                                        $normal_price = ($price_non_tax + $tax);
                                        $final_price = ($price_non_tax + $tax) - $promotion_price;
                                    }
                                }else {
                                    $normal_price = ($price_non_tax + $tax);
                                    $final_price = ($price_non_tax + $tax);
                                }
                            @endphp
                            <div class="card">
                                <div class="image-container">
                                    <div class="first">
                                        <ul class="card-lable">
                                            <li class="item">
                                                <div class="meta-box">
                                                    <p class="text"><i class="icon-copy fa fa-map-marker" aria-hidden="true"></i> {{ $activity->location }}</p>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                    @if (isset($bookingcode->code) or $promotion_price>0)
                                        <div class="price-card-normal m-t-8">
                                            {{"$ " . number_format($normal_price) }}
                                        </div>
                                        <div class="price-card m-t-27">
                                            {{"$ " . number_format($final_price) }}
                                        </div>
                                    @else
                                        <div class="price-card m-t-8">
                                            {{"$ " . number_format($final_price) }}
                                        </div>
                                    @endif
                                    @if (isset($bookingcode->code))
                                        <a href="/activity-{{ $activity->code }}-{{ $bookingcode->code }}">
                                    @else
                                        <a href="/activity-{{ $activity->code }}">
                                    @endif
                                        <img src="{{ asset('storage/activities/activities-cover/' . $activity->cover) }}" class="img-fluid rounded thumbnail-image">
                                        <div class="card-detail-title">{{ $activity->name }}</div>
                                    </a>
                                </div>
                            </div>
                            
                        @endforeach
                    </div>
                @endif
            </div>
            @include('layouts.footer')
        </div>
    </div>
@endsection
