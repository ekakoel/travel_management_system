@extends('layouts.head')
@section('title', __('messages.Admin Panel'))
@section('content')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="card-box">
                    <div class="row align-items-center">
                        <div class="col-md-4 m-b-18 img-panel">
                            <img src="images/property/admin-panel.png" alt="Welcome">
                        </div>
                        <div class="col-md-8">
                            <div class="welcome-subtitle">
                                Welcome to Admin Panel
                            </div>
                            <div class="welcome-title p-b-18">
                                {{ Auth::user()->name }}
                            </div>
                            <div class="welcome-text m-b-18">
                                <p>
                                    This page can only be seen by administrators, this page displays comprehensive information on available system data.
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
                <div class="row m-b-30">
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 mobile">
                        <div class="row">
                            @include('admin.usd-rate')
                            @include('layouts.attentions')
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div id="services" class="col-md-12">
                                @include('layouts.services-admin-panel')
                            </div>
                            <div id="orders" class="col-md-12">
                                @include('layouts.orders-admin-panel')
                            </div>
                            <div id="uiConfig" class="col-md-12">
                                @include('layouts.ui-config-admin-panel')
                            </div>
                        </div>
                    </div>
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 desktop">
                        <div class="row">
                            @include('admin.usd-rate')
                            @include('layouts.attentions')
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endcan
@endsection