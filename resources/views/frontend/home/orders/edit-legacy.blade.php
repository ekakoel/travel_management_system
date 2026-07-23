@section('title', __('messages.Orders'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        @if ($order->status == "Draft")
                            <div class="title"><i class="icon-copy fa fa-tags"></i>&nbsp; @lang('messages.Order')</div>
                        @else
                            <div class="title"><i class="icon-copy fa fa-tags"></i>&nbsp; @lang('messages.Detail Order')</div>
                        @endif
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/dashboard">@lang('messages.Dashboard')</a></li>
                                <li class="breadcrumb-item"><a href="/orders">@lang('messages.Order')</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $order->orderno }}</a></li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
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
            </div>
            <div class="row">
                @if ($order->service == "Hotel" or $order->service == "Hotel Promo" or $order->service == "Hotel Package")
                    @include('frontend.home.orders.partials.edit-hotel')
                @elseif($order->service == "Activity")
                    @include('frontend.home.orders.partials.edit-activity')
                @elseif($order->service == "Transport")
                    @include('frontend.home.orders.partials.edit-transport')
                @elseif($order->service == "Private Villa")
                    @include('frontend.home.orders.partials.edit-villa')
                @endif
            </div>
            @include('layouts.footer')
        </div>
    </div>
    <script>
        $(document).ready(function() {
        $("#edit-order").submit(function() {
            $(".result").text("");
            $(".loading-icon").removeClass("hidden");
            $(".submit").attr("disabled", true);
            $(".btn-txt").text("Processing ...");
        });
        });
    </script>
@endsection
