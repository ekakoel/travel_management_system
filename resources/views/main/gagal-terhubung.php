@section('title', __('messages.Gagal Terhubung'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="row">
                <div class="col-md-3 m-b-18">
                    <div class="height-100-p widget-style1">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="chart-icon">
                                <i class="icon-copy dw dw-hotel-o" aria-hidden="true"></i>
                            </div>
                            <div class="widget-data">
                                <div class="widget-data-title">{{ $activehotels->count() }} @lang('messages.Hotels')</div>
                                <div class="widget-data-subtitle">@lang("messages.Available")</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-box m-b-18">
                <div class="error-msg">
                    <div class="error-title">
                        404
                    </div>
                    <div class="error-subtitle">
                        halaman yang anda cari tidak dapat ditemukan
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
