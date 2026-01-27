@section('title', __('messages.Wedding Detail'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title">
                            @if ($service != "")
                                {!! $service->icon !!} {{ $service->name }}
                            @endif
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                <li class="breadcrumb-item"><a href="/weddings-admin">{{ $service->name }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $wedding->name }}</li>
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
                    <div class="product-wrap">
                        <form id="activatePackage" action="/factivate-wedding-package/{{ $wedding->id }}" method="post" enctype="multipart/form-data">
                            @method('put')
                            {{ csrf_field() }}
                            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                        </form>
                        <form id="draftedPackage" action="/fdrafted-wedding-package/{{ $wedding->id }}" method="post" enctype="multipart/form-data">
                            @method('put')
                            {{ csrf_field() }}
                            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                        </form>
                        <form id="removePackage" action="/fremove-wedding-package/{{ $wedding->id }}" method="post" enctype="multipart/form-data">
                            @method('put')
                            {{ csrf_field() }}
                            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                        </form>
                        <div class="product-detail-wrap">
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
                                            <div class="subtitle"><i class="icon-copy fi-torso-business"></i><i class="icon-copy fi-torso-female"></i>{{ $wedding->name }}</div>
                                            <div class="status-card">
                                                @if ($wedding->status == "Rejected")
                                                    <div class="status-rejected"></div>
                                                @elseif ($wedding->status == "Invalid")
                                                    <div class="status-invalid"></div>
                                                @elseif ($wedding->status == "Active")
                                                    <div class="status-active"></div>
                                                @elseif ($wedding->status == "Waiting")
                                                    <div class="status-waiting"></div>
                                                @elseif ($wedding->status == "Draft")
                                                    <div class="status-draft"></div>
                                                @elseif ($wedding->status == "Archived")
                                                    <div class="status-archived"></div>
                                                @else
                                                @endif
                                            </div>
                                        </div>
                                        <div class="page-card">
                                            <figure class="card-banner">
                                                <img src="{{ asset ('storage/weddings/wedding-cover/' . $wedding->cover) }}" alt="{{ $wedding->name }}" loading="lazy">
                                            </figure>
                                            <div class="card-content">
                                                <div class="card-text">
                                                    <div class="row ">
                                                        <div class="col-4">
                                                            <div class="card-title"><i class="icon-copy dw dw-hotel" aria-hidden="true"></i>Hotel</div>
                                                            <div class="data-web">{{  $hotel->name  }}</div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="card-title"><i class="icon-copy fa fa-institution" aria-hidden="true"></i>Wedding Venue</div>
                                                            <div class="data-web">{{  $wedding_venue->name }}</div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="card-title"><i class="icon-copy fa fa-users" aria-hidden="true"></i>Capacity</div>
                                                            <div class="data-web">{{  $wedding->capacity }} guests</div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="card-title"><i class="icon-copy fa fa-calendar-check-o" aria-hidden="true"></i>@lang('messages.Duration')</div>
                                                            <div class="data-web">{{  $wedding->duration }} </div>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="card-title"><i class="icon-copy fa fa-calendar" aria-hidden="true"></i>@lang('messages.Period')</div>
                                                            <div class="data-web">{{  date("m/d/y",strtotime($wedding->period_start))." - ".date("m/d/y",strtotime($wedding->period_end)) }} </div>
                                                        </div>
                                                       @if ($wedding->pdf)
                                                            <div class="col-12 m-t-8">
                                                                <div class="card-title"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> PDF Rate</div>
                                                                <a href="#" data-target="#wedding-pdf-{{ $wedding->id }}" data-toggle="modal">
                                                                    <div class="icon-list" data-toggle="tooltip" data-placement="top" title="View PDF Rate">
                                                                        {{ $wedding->name }} PDF
                                                                    </div>
                                                                </a>
                                                                {{-- Modal Property PDF ----------------------------------------------------------------------------------------------------------- --}}
                                                                <div class="modal fade" id="wedding-pdf-{{ $wedding->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                                        <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                                            <div class="modal-body pd-5">
                                                                                <embed src="storage/weddings/wedding-pdf/{{ $wedding->pdf }}" frameborder="10" width="100%" height="850px">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                {{-- End Modal Contract ----------------------------------------------------------------------------------------------------------- --}}
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @if ($wedding->description)
                                                <div class="col-md-12">
                                                    <div class="box-paragraf-title">Description</div>
                                                    <div class="box-paragraf">
                                                        {!! $wedding->description !!}
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($wedding->include)
                                                <div class="col-md-12">
                                                    <div class="box-paragraf-title">Include</div>
                                                    <div class="box-paragraf">
                                                        {!! $wedding->include !!}
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($wedding->additional_info)
                                                <div class="col-md-12">
                                                    <div class="box-paragraf-title">Additional Information</div>
                                                    <div class="box-paragraf">
                                                        {!! $wedding->additional_info !!}
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($wedding->cancellation_policy)
                                                <div class="col-md-12">
                                                    <div class="box-paragraf-title">Cancellation Policy</div>
                                                    <div class="box-paragraf">
                                                        {!! $wedding->cancellation_policy !!}
                                                    </div>
                                                </div>
                                            @endif
                                            @if ($wedding->remark)
                                                <div class="col-md-12">
                                                    <div class="box-paragraf-title">Remark</div>
                                                    <div class="box-paragraf">
                                                        {!! $wedding->remark !!}
                                                    </div>
                                                </div>
                                            @endif
                                            <div class="col-md-12">
                                                <div class="tab">
                                                    <ul class="nav nav-tabs" role="tablist">
                                                        @if ($wedding->description)
                                                            <li class="nav-item">
                                                                <a class="nav-link active"  data-toggle="tab" href="#description-text" role="tab" aria-selected="false">@lang('messages.Description')</a>
                                                            </li>
                                                        @endif
                                                        @if ($wedding->include)
                                                            <li class="nav-item">
                                                                <a class="nav-link"  data-toggle="tab" href="#include-text" role="tab" aria-selected="false">@lang('messages.Include')</a>
                                                            </li>
                                                        @endif
                                                        @if ($wedding->additional_info)
                                                            <li class="nav-item">
                                                                <a class="nav-link"  data-toggle="tab" href="#additional-info-text" role="tab" aria-selected="false">@lang('messages.Additional Information')</a>
                                                            </li>
                                                        @endif
                                                        @if ($wedding->cancellation_policy)
                                                            <li class="nav-item">
                                                                <a class="nav-link"  data-toggle="tab" href="#cancellation-policy-text" role="tab" aria-selected="false">@lang('messages.Cancellation Policy')</a>
                                                            </li>
                                                        @endif
                                                    </ul>
                                                    <div class="tab-content">
                                                        @if ($wedding->include)
                                                            <div class="tab-pane fade" id="include-text" role="tabpanel">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="tab-inner-title">Include</div>
                                                                        <div class="data-web-text-area">
                                                                            {!! $wedding->include !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($wedding->description)
                                                            <div class="tab-pane fade active show" id="description-text" role="tabpanel">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="tab-inner-title">Description</div>
                                                                        <div class="data-web-text-area">
                                                                            {!! $wedding->description !!}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($wedding->additional_info or $wedding->payment_process or $wedding->payment_process)
                                                            <div class="tab-pane fade" id="additional-info-text" role="tabpanel">
                                                                <div class="row">
                                                                    @if ($wedding->additional_info)
                                                                        <div class="col-md-12">
                                                                            <div class="tab-inner-title">Additional Information</div>
                                                                            <div class="data-web-text-area">{!! $wedding->additional_info !!} </div>
                                                                        </div>
                                                                    @endif
                                                                    @if ($wedding->executive_staff)
                                                                        <div class="col-md-12">
                                                                            <div class="tab-inner-title">Executive Staff</div>
                                                                            <div class="data-web-text-area">{!! $wedding->executive_staff !!} </div>
                                                                        </div>
                                                                    @endif
                                                                    @if ($wedding->payment_process)
                                                                        <div class="col-md-12">
                                                                            <div class="tab-inner-title">Payment Process</div>
                                                                            <div class="data-web-text-area">{!! $wedding->payment_process !!} </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endif
                                                        @if ($wedding->cancellation_policy)
                                                            <div class="tab-pane fade" id="cancellation-policy-text" role="tabpanel">
                                                                <div class="row">
                                                                    <div class="col-12">
                                                                        <div class="tab-inner-title">Cancellation Policy</div>
                                                                        <div class="data-web-text-area">{!! $wedding->cancellation_policy !!} </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @if ($wedding->status !== "Active")
                                            <div class="card-box-footer">
                                                <a href="/{{ $service->nicname }}-edit-{{ $wedding['id'] }}"><button class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button></a>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4 desktop">
                                    <div class="row">
                                        @include('admin.usd-rate')
                                        @include('layouts.attentions')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endcan
@endsection
