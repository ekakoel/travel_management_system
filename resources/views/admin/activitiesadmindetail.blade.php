@section('title', __('messages.Activity Detail'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        @php
            $usd_activity =ceil($activity->contract_rate / $usdrates->rate);
            $usd_activity_markup = $usd_activity + $activity->markup;
            $tax_activity = $taxes->tax / 100;
            $usd_activity_tax = ceil($usd_activity_markup * $tax_activity);
            $final_price = $usd_activity_markup + $usd_activity_tax;
        @endphp
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title">
                            <i class="icon-copy fa fa-child" aria-hidden="true"></i> Activity
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                @if (isset($activity->partners_id))
                                    <li class="breadcrumb-item"><a href="/detail-partner-{{ $activity->partners_id }}">{{ $partner->name }}</a></li>
                                @else
                                    <li class="breadcrumb-item">?</li>
                                @endif
                                <li class="breadcrumb-item"><a href="/activities-admin">Activities</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $activity->name }}</li>
                            </ol>
                        </nav>
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
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">{{ $activity->name }}</div>
                                    <div class="status-card">
                                        @if ($activity->status == "Rejected")
                                            <div class="status-rejected"></div>
                                        @elseif ($activity->status == "Invalid")
                                            <div class="status-invalid"></div>
                                        @elseif ($activity->status == "Active")
                                            <div class="status-active"></div>
                                        @elseif ($activity->status == "Waiting")
                                            <div class="status-waiting"></div>
                                        @elseif ($activity->status == "Draft")
                                            <div class="status-draft"></div>
                                        @elseif ($activity->status == "Archived")
                                            <div class="status-archived"></div>
                                        @else
                                        @endif
                                    </div>
                                </div>
                                <div class="page-card">
                                    <div class="card-banner">
                                        <img src="{{ asset ('storage/activities/activities-cover/' . $activity->cover) }}" alt="{{ $activity->name }}" loading="lazy">
                                    </div>
                                    <div class="card-content">
                                        <div class="data-web">
                                            <a target="__blank" href="{{ $activity->map }}">
                                                <div class="data-web"><i class="icon-copy fa fa-map-marker" aria-hidden="true"></i> {{ " ".$activity->location }}</div>
                                            </a>
                                        </div>
                                        <hr class="form-hr">
                                        <div class="card-text">
                                            <div class="row ">
                                                <div class="col-4">
                                                    <div class="card-subtitle">Partner:</div>
                                                    <p>
                                                        @if (isset($activity->partners_id))
                                                            <a href="/detail-partner-{{ $partner->id }}">
                                                                {{ $partner->name }}
                                                            </a>
                                                        @else
                                                            -
                                                        @endif
                                                    </p>
                                                </div>
                                                <div class="col-4">
                                                    <div class="card-subtitle">Duration:</div>
                                                    <p>{{ $activity->duration }}</p>
                                                </div>
                                                <div class="col-4">
                                                    <div class="card-subtitle">Capacity:</div>
                                                    <p>{{ $activity->qty." Pax" }}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <hr class="form-hr">
                                        <div class="row ">
                                            <div class="col-6 ">
                                                <div class="card-subtitle">Contract Rate :</div>
                                                <div class="rate-usd">{{ currencyFormatUsd($usd_activity) }}</div>
                                                <div class="rate-idr m-b-8">{{ currencyFormatIdr($activity->contract_rate) }}</div>
                                            </div>
                                            <div class="col-6 ">
                                                <div class="card-subtitle">Markup :</div>
                                                <div class="rate-usd">{{ "$ ". $activity->markup }}</div>
                                                <div class="rate-idr m-b-8">{{currencyFormatIdr($activity->markup * $usdrates->rate) }}</div>
                                            </div>
                                            <div class="col-6">
                                                <div class="card-subtitle">Tax :</div>
                                                <div class="rate-usd">{{ "$ ". $usd_activity_tax." (". $taxes->tax."%)"}}</div>
                                                <div class="rate-idr m-b-8">{{currencyFormatIdr($usd_activity_tax * $usdrates->rate) }}</div>
                                            </div>
                                            <div class="col-6">
                                                <div class="card-subtitle">Published Rate :</div>
                                                <div class="rate-usd">{{ "$ ".  number_format($final_price) }}</div>
                                                <div class="rate-idr">{{currencyFormatIdr($final_price * $usdrates->rate) }}</div>
                                            </div>
                                            <div class="col-6">
                                                <div class="card-subtitle">Valid Until :</div>
                                                <p>{{ date('d M Y', strtotime($activity->validity)) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    @if ($activity->description != "")
                                        <div class="col-md-12">
                                            <div class="card-text">
                                                <div class="card-subtitle">Description:</div>
                                                <p>{!! $activity->description !!}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if (isset($activity->itinerary))
                                        <div class="col-md-12">
                                            <div class="card-text">
                                                <div class="card-subtitle">Itinerary:</div>
                                                <p>{!! $activity->itinerary !!}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($activity->include != "")
                                        <div class="col-md-12">
                                            <div class="card-text">
                                                <div class="card-subtitle">Include:</div>
                                                <p>{!! $activity->include !!}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if ($activity->additional_info != "")
                                        <div class="col-md-12">
                                            <div class="card-text">
                                                <div class="card-subtitle">Additional Information:</div>
                                                <p>{!! $activity->additional_info !!}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                @if ($activity->cancellation_policy != "")
                                    <div class="detail-item">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="card-text">
                                                    <div class="card-subtitle">Cancellation Policy:</div>
                                                    {!! $activity->cancellation_policy !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="card-box-footer">
                                    @canany(['posDev','posAuthor'])
                                        <a href="/edit-activity-{{ $activity['id'] }}"><button class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button></a>
                                    @endcanany
                                    <a href="/activities-admin"><button class="btn btn-secondary" ><i class="icon-copy fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
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
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endcan
@endsection
