@extends('layouts.head')
@section('title', __('messages.Tour'))
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
                    {{-- ATTENTIONS --}}
                    <div class="row">
                        @include('layouts.attentions')
                    </div>
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