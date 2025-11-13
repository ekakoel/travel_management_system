@section('title', __('messages.Orders'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20 xs-pd-20-10">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title"><i class="icon-copy fa fa-tags"></i>&nbsp; @lang('messages.Orders')</div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/dashboard">@lang('messages.Dashboard')</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">@lang('messages.Orders')</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="info-action">
                        @if (count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                        @if (\Session::has('success'))
                            <div class="alert alert-success">
                                <ul>
                                    <li>{!! \Session::get('success') !!}</li>
                                </ul>
                            </div>
                        @endif
                        @if (\Session::has('warning'))
                            <div class="alert alert-danger">
                                <ul>
                                    <li>{!! \Session::get('warning') !!}</li>
                                </ul>
                            </div>
                        @endif
                    </div>
                    @if (Auth::user()->email == "" or Auth::user()->phone == "" or Auth::user()->office == "" or Auth::user()->address == "" or Auth::user()->country == "")
                        <div class="col-xl-12">
                            <div class="card-box">
                                <div class="notification-box">
                                    <i class="icon-copy fa fa-exclamation-triangle" aria-hidden="true"></i>
                                   @lang('messages.Please complete your profile data first to be able to submit orders, by clicking this link') -> <a href="/profile">@lang('messages.Edit Profile')</a>
                                </div>
                            </div>
                        </div>
                    @endif
                    @if (count($rejectedorders) > 0)
                        <div class="col-xl-12 ">
                            <div class="card-box">
                                <p class="alert-text">
                                    <i style="color: red;"><i class="icon-copy fa fa-info-circle" aria-hidden="true"></i> @lang('messages.you have') {{ count($rejectedorders) }} @lang('messages.rejected order').<br>@lang('messages.Your order was rejected due to various factors, your profile data is incomplete, we cannot contact your telephone number or email to verify your order')<br> @lang('messages.Please complete your profile data with actual data to simplify the verification process of your order!') </i>
                                </p>
                            </div>
                        </div>
                    @endif
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 mobile">
                        <div class="row">
                            @include('layouts.attentions')
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            @if(count($tourorders) > 0 || count($hotelorders) > 0 || count($activityorders) > 0 || count($transportorders) > 0 || count($weddingorders) > 0 || count($villaorders) > 0)
                                @if (count($hotelorders) > 0)
                                    @include('layouts.order-hotel')
                                @endif
                                @if (count($villaorders) > 0)
                                    @include('layouts.order-villa')
                                @endif
                                @if (count($tourorders) > 0)
                                    @include('layouts.order-tour')
                                @endif
                                @if (count($activityorders) > 0)
                                    @include('layouts.order-activity')
                                @endif
                                @if (count($transportorders) > 0)
                                    @include('layouts.order-transport')
                                @endif
                                @if (count($weddingorders) > 0)
                                    @include('layouts.order-wedding')
                                @endif
                                @if (count($orderhistories) > 0)
                                    @include('layouts.order-history')
                                @endif
                            @else
                                <div class="col-xl-12">
                                    <div class="card-box p-b-18">
                                        <div class="notification">@lang('messages.At this time you do not have Orders!')</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 desktop">
                        <div class="row">
                            @include('layouts.attentions')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
