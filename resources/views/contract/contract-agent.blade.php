@section('title', __('messages.Order'))
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
                    
                </div>
            </div>
        </div>
    </div>
@endsection
