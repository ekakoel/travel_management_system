@section('title', __('messages.Download'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @if (Auth::User()->status == "Active")
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="card-box m-b-18">
                    <div class="row align-items-center">
                        <div class="col-md-4 m-b-18 img-panel">
                            <img src="images/property/database.png" alt="Welcome">
                        </div>
                        <div class="col-md-8">
                        
                            <div class="welcome-title p-b-18">
                                @lang('messages.Download Area')
                            </div>
                            <div class="welcome-text m-b-18">
                                <p>
                                    @lang('messages.On this page you can download data for each service provided by') {{ config('app.business') }}
                                </p>
                            </div>
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
                     {{-- ATTENTIONS --}}
                     <div class="col-md-4 mobile">
                        <div class="row">
                            @include('admin.usd-rate')
                            @include('layouts.attentions')
                        </div>
                    </div>
                    <div class="col-md-8 m-b-18">
                        <div class="card-box mb-30">
                            <div class="card-box-title">
                                <div class="title">@lang('messages.Pricelist')</div>
                            </div>
                            <div class="pb-20">
                                <table class="table hover data-table-export">
                                    <thead>
                                        <tr>
                                            <th class="width:3%">@lang('messages.No')</th>
                                            <th>@lang('messages.Database')</th>
                                            <th>@lang('messages.Action')</th>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                       @if (count($hotel_prices) > 0)
                                            <tr>
                                                <td class="table-plus">1</td>
                                                <td>@lang('messages.Recent Hotel Pricelist')</td>
                                                <td>
                                                    <a href="/download-data-hotel">
                                                        <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                        @if (count($hotel_promo) > 0)
                                            <tr>
                                                <td class="table-plus">2</td>
                                                <td>@lang('messages.Recent Hotel Promo Pricelist')</td>
                                                <td>
                                                    <a href="/download-data-hotel-promo">
                                                        <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                        @if (count($hotel_package) > 0)
                                            <tr>
                                                <td class="table-plus">3</td>
                                                <td>@lang('messages.Recent Hotel Package Pricelist')</td>
                                                <td>
                                                    <a href="/download-data-hotel-package">
                                                        <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Export Datatable End -->
                    </div>
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 desktop">
                        <div class="row">
                            @include('layouts.attentions')
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endif
@endsection