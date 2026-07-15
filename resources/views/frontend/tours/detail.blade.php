@extends('layouts.head')
@section('title', __('messages.Tour'))
@php
    $hasTourRouteMap = !empty($tourMapLocations);
@endphp
@if ($hasTourRouteMap)
    @push('styles')
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
            integrity="sha256-p4NxAoJBhIINfQWTKf0dQYfdh4A8iSrlv6b6R64ORc4=" crossorigin="">
        <style>
            .tour-route-map {
                border: 1px solid rgba(15, 23, 42, 0.08);
                border-radius: 24px;
                background: linear-gradient(145deg, #ffffff 0%, #f7fbf7 100%);
                box-shadow: 0 18px 45px rgba(15, 23, 42, 0.08);
                margin: 0 0 24px;
                overflow: hidden;
            }
            .tour-route-map__header {
                align-items: flex-start;
                display: flex;
                gap: 14px;
                justify-content: space-between;
                padding: 22px 24px 16px;
            }
            .tour-route-map__eyebrow {
                color: #7a8f3b;
                font-size: 12px;
                font-weight: 800;
                letter-spacing: 0.12em;
                margin-bottom: 6px;
                text-transform: uppercase;
            }
            .tour-route-map__title {
                color: #16213e;
                font-size: 24px;
                font-weight: 800;
                line-height: 1.2;
                margin: 0;
            }
            .tour-route-map__subtitle {
                color: #667085;
                margin: 8px 0 0;
                max-width: 680px;
            }
            .tour-route-map__count {
                align-items: center;
                background: #eef6d2;
                border-radius: 999px;
                color: #526b1d;
                display: inline-flex;
                flex: 0 0 auto;
                font-size: 13px;
                font-weight: 800;
                gap: 8px;
                padding: 10px 14px;
                white-space: nowrap;
            }
            .tour-route-map__canvas {
                background: #edf3ee;
                height: 420px;
                min-height: 360px;
                width: 100%;
            }
            .tour-route-map__legend {
                display: grid;
                gap: 12px;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                padding: 18px 24px 24px;
            }
            .tour-route-map__stop {
                background: #fff;
                border: 1px solid rgba(15, 23, 42, 0.08);
                border-radius: 18px;
                display: flex;
                gap: 12px;
                padding: 14px;
            }
            .tour-route-map__marker {
                align-items: center;
                background: #7a8f3b;
                border-radius: 50%;
                color: #fff;
                display: inline-flex;
                flex: 0 0 34px;
                font-size: 13px;
                font-weight: 800;
                height: 34px;
                justify-content: center;
                width: 34px;
            }
            .tour-route-map__stop-title {
                color: #16213e;
                font-weight: 800;
                margin: 0 0 4px;
            }
            .tour-route-map__stop-meta,
            .tour-route-map__stop-desc {
                color: #667085;
                font-size: 13px;
                margin: 0;
            }
            .tour-route-map__link {
                color: #7a8f3b;
                display: inline-block;
                font-size: 13px;
                font-weight: 800;
                margin-top: 8px;
            }
            .tour-route-map__pin {
                align-items: center;
                background: #7a8f3b;
                border: 3px solid #fff;
                border-radius: 50%;
                box-shadow: 0 10px 24px rgba(15, 23, 42, 0.25);
                color: #fff;
                display: flex;
                font-size: 13px;
                font-weight: 800;
                height: 34px;
                justify-content: center;
                width: 34px;
            }
            .tour-route-map__popup-title {
                color: #16213e;
                font-weight: 800;
                margin-bottom: 4px;
            }
            .tour-route-map__popup-meta,
            .tour-route-map__popup-desc {
                color: #667085;
                font-size: 12px;
                margin: 0 0 6px;
            }
            @media (max-width: 767px) {
                .tour-route-map__header {
                    display: block;
                    padding: 18px;
                }
                .tour-route-map__count {
                    margin-top: 12px;
                }
                .tour-route-map__canvas {
                    height: 340px;
                    min-height: 320px;
                }
                .tour-route-map__legend {
                    grid-template-columns: 1fr;
                    padding: 14px 18px 18px;
                }
            }
        </style>
    @endpush
    @push('scripts')
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
            integrity="sha256-20nQCchB9co0qIjGwZ5i6JSJ9XH2bfOQFh++Swhb0tM=" crossorigin=""></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const mapElement = document.getElementById('tourRouteMap');
                const locations = @json($tourMapLocations);

                if (!mapElement || !window.L || mapElement.dataset.initialized === 'true' || !locations.length) {
                    return;
                }

                mapElement.dataset.initialized = 'true';

                const map = L.map(mapElement, {
                    scrollWheelZoom: false,
                    zoomControl: true
                });

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                    maxZoom: 19
                }).addTo(map);

                const bounds = [];

                locations.forEach(function (location) {
                    const latLng = [location.lat, location.lng];
                    bounds.push(latLng);

                    const marker = L.marker(latLng, {
                        icon: L.divIcon({
                            className: '',
                            html: '<span class="tour-route-map__pin">' + location.order + '</span>',
                            iconSize: [34, 34],
                            iconAnchor: [17, 17],
                            popupAnchor: [0, -18]
                        })
                    }).addTo(map);

                    const popup = document.createElement('div');
                    const title = document.createElement('div');
                    const meta = document.createElement('p');

                    title.className = 'tour-route-map__popup-title';
                    title.textContent = location.name;
                    meta.className = 'tour-route-map__popup-meta';
                    meta.textContent = '{{ __('tour-map.day') }} ' + location.day + ' · {{ __('tour-map.stop') }} ' + location.visit_order;

                    popup.appendChild(title);
                    popup.appendChild(meta);

                    if (location.description) {
                        const description = document.createElement('p');
                        description.className = 'tour-route-map__popup-desc';
                        description.textContent = location.description;
                        popup.appendChild(description);
                    }

                    if (location.google_maps_url) {
                        const link = document.createElement('a');
                        link.href = location.google_maps_url;
                        link.target = '_blank';
                        link.rel = 'noopener noreferrer';
                        link.textContent = '{{ __('tour-map.open_google_maps') }}';
                        popup.appendChild(link);
                    }

                    marker.bindPopup(popup);
                });

                if (bounds.length > 1) {
                    L.polyline(bounds, {
                        color: '#7a8f3b',
                        opacity: 0.8,
                        weight: 4,
                        dashArray: '8 10'
                    }).addTo(map);
                    map.fitBounds(bounds, { padding: [36, 36] });
                } else {
                    map.setView(bounds[0], 13);
                }

                setTimeout(function () {
                    map.invalidateSize();
                }, 250);
            });
        </script>
    @endpush
@endif
@section('content')
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
                <div class="col-md-8">
                    <div class="card-box m-b-18">
                        <div class="card-box-title">
                            <div class="subtitle">
                                <i class="fa fa-file-text"></i>{{ $tour->$langName }}
                                <span>{{ $tour->type?->$langType }}</span>
                            </div>
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
                                @if ($hasTourRouteMap)
                                    <section class="tour-route-map" aria-labelledby="tourRouteMapTitle">
                                        <div class="tour-route-map__header">
                                            <div>
                                                <div class="tour-route-map__eyebrow">@lang('tour-map.overview')</div>
                                                <h2 class="tour-route-map__title" id="tourRouteMapTitle">@lang('tour-map.title')</h2>
                                                <p class="tour-route-map__subtitle">@lang('tour-map.subtitle')</p>
                                            </div>
                                            <div class="tour-route-map__count">
                                                <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>
                                                {{ count($tourMapLocations) }} @lang('tour-map.planned_stops')
                                            </div>
                                        </div>
                                        <div id="tourRouteMap" class="tour-route-map__canvas" role="img" aria-label="@lang('tour-map.title')"></div>
                                        <div class="tour-route-map__legend" aria-label="@lang('tour-map.visit_sequence')">
                                            @foreach ($tourMapLocations as $mapLocation)
                                                <article class="tour-route-map__stop">
                                                    <span class="tour-route-map__marker">{{ $mapLocation['order'] }}</span>
                                                    <div>
                                                        <p class="tour-route-map__stop-title">{{ $mapLocation['name'] }}</p>
                                                        <p class="tour-route-map__stop-meta">
                                                            @lang('tour-map.day') {{ $mapLocation['day'] }}
                                                            &middot;
                                                            @lang('tour-map.stop') {{ $mapLocation['visit_order'] }}
                                                        </p>
                                                        @if (!empty($mapLocation['description']))
                                                            <p class="tour-route-map__stop-desc">{{ $mapLocation['description'] }}</p>
                                                        @endif
                                                        @if (!empty($mapLocation['google_maps_url']))
                                                            <a class="tour-route-map__link" href="{{ $mapLocation['google_maps_url'] }}" target="_blank" rel="noopener noreferrer">
                                                                @lang('tour-map.open_google_maps')
                                                            </a>
                                                        @endif
                                                    </div>
                                                </article>
                                            @endforeach
                                        </div>
                                    </section>
                                @endif
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
                        <div class="card-box-footer">
                        </div>
                    </div>
                </div>
                <div class="col-md-4 m-b-18">
                    <div class="card-box">
                        <div class="card-box-title">
                            <div class="subtitle">
                                <i class="icon-copy dw dw-price-tag"></i>
                                @lang('messages.Price')
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table stripe ">
                                    <tr>
                                        <td class="width:5%;"><b>#</b></td>
                                        <td class="width:15%;"><b>@lang('messages.Number of Guests')</b></td>
                                        <td class="width:80%;"><b>@lang('messages.Price')/@lang('messages.pax')</b></td>
                                    </tr>
                                    @foreach ($tour->prices as $pr_no=>$tour_price)
                                        <tr>
                                            <td>{{ ++$pr_no }}</td>
                                            <td>{{ $tour_price->min_qty." - ".$tour_price->max_qty }} @lang('messages.pax')</td>
                                            <td>{{ currencyFormatUsd($tour_price->calculated_price) }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            </div>
                        </div>
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
@endsection
