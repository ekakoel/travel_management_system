@section('title', __('messages.Transports'))
@section('content')
    @extends('layouts.head')
{{-- @include('component.sysload') --}}
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="row">
                <div class="col-md-12">
                    <div class="promotion-bookingcode">
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
                    </div>
                </div>
            <div class="col-md-12">
                <div class="card-box">
                    <div class="card-box-title">
                        <div class="subtitle"><i class="icon-copy fa fa-car" aria-hidden="true"></i> @lang("messages.Transport")</div>
                    </div>
                    <form action="/search-transports" method="POST" role="search";>
                        {{ csrf_field() }}
                        <div class="search-container flex-end">
                            <div class="search-item">
                                <input type="text" class="form-control" name="brand" placeholder="@lang('messages.Search by brand')" value="{{ old('brand') }}">
                            </div>
                            <div class="search-item">
                                <select name="type" value="{{ old('type') }}" class="custom-select col-12 @error('type') is-invalid @enderror">
                                    <option selected value="">@lang('messages.Search by type')</option>
                                    @foreach ($type as $type)
                                        <option value="{{ $type->type }}">@lang('messages.'.$type->type)</option>
                                    @endforeach
                                </select>
                                @error('type')
                                    <div class="alert alert-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn-search btn-primary"><i class='icon-copy fa fa-search' aria-hidden='true'></i> @lang('messages.Search')</button>
                        </div>
                    </form>
                    <div class="card-box-content">
                        @foreach ($transports as $transport)
                            <div class="card">
                                <div class="image-container">
                                    <div class="first">
                                        <div class="card-lable">
                                            <div class="meta-box">
                                                <p><i class="icon-copy ion-ios-person"></i> {{ $transport->capacity. " " }}@lang('messages.Seat')</p>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="/transport-{{ $transport->code }}">
                                        <img src="{{ getThumbnail('/transports/transports-cover/' . $transport->cover,320,200) }}" class="img-fluid rounded thumbnail-image">
                                        <div class="front-image-title">
                                            <p>{{ $transport->name }}</p>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @include('layouts.footer')
    </div>
@endsection
