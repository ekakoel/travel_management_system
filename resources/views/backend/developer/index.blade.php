@extends('layouts.head')
@section('title', __('messages.Admin Panel'))
@section('content')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="title">
                    <h2 class="h3 mb-0">Services</h2>
                </div>
                <div class="row pb-10">
                    @foreach ($services as $service)
                        <div class="col-xl-3 col-lg-3 col-md-6 mb-20">
                            <div class="main-card-box height-100-p widget-style3">
                                <div class="d-flex flex-wrap h-100">
                                    <div class="widget-data">
                                        <div class="weight-700 font-24 text-dark"> {{ $service->name }}</div>
                                        @if ($service->name == "Hotels")
                                            <div class="font-14 text-secondary weight-500">Active {{ count($hotels_active) }} ~ Draft {{ count($hotels_inactive) }}</div>
                                        @elseif ($service->name == "Tours")
                                            <div class="font-14 text-secondary weight-500">Active {{ count($tours_active) }} ~ Draft {{ count($tours_inactive) }}</div>
                                        @elseif ($service->name == "Activities")
                                            <div class="font-14 text-secondary weight-500">Active {{ count($activities_active) }} ~ Draft {{ count($activities_inactive) }}</div>
                                        @elseif ($service->name == "Transports")
                                            <div class="font-14 text-secondary weight-500">Active {{ count($transports_active) }} ~ Draft {{ count($transports_inactive) }}</div>
                                        @elseif ($service->name == "Villas")
                                            <div class="font-14 text-secondary weight-500">Active {{ count($villas_active) }} ~ Draft {{ count($villas_inactive) }}</div>
                                        @endif
                                    </div>
                                    <div class="widget-icon">
                                        <div class="icon" data-color="#00eccf"><i class="icon-copy {{ $service->icon }}"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="bg-white rounded-xl shadow p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">
                            Hotel Price Overview
                        </h2>

                        <select id="hotelSelect" class="border rounded px-3 py-1 text-sm">
                            <option value="">Select Hotel</option>
                            @foreach($hotels as $hotel)
                                <option value="{{ $hotel->id }}">{{ $hotel->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <canvas id="priceChart" height="90"></canvas>
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
                                @include('backend.developer.partials.services-session')
                            </div>
                            <div id="orders" class="col-md-12">
                                @include('backend.developer.partials.order-session')
                            </div>
                            <div id="uiConfig" class="col-md-12">
                                @include('backend.developer.partials.ui-config-session')
                            </div>
                        </div>
                    </div>
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 desktop">
                        <div class="row">
                            @include('backend.partials.usd-rate-session')
                            @include('layouts.attentions')
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endcan
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        let chart;

        const ctx = document.getElementById('priceChart').getContext('2d');

        chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Contract Rate',
                    data: [],
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59,130,246,0.15)',
                    tension: 0.4,
                    fill: true,
                    pointRadius: 5
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: true }
                }
            }
        });

        document.getElementById('hotelSelect').addEventListener('change', function () {
            let hotelId = this.value;

            if (!hotelId) return;

            fetch(`/dashboard/hotel-price-chart?hotel_id=${hotelId}`)
                .then(res => res.json())
                .then(data => {
                    chart.data.labels = data.months;
                    chart.data.datasets[0].data = data.values;
                    chart.update();
                });
        });
    </script>


@endsection