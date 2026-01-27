@section('title', __('messages.Orders'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title"><i class="icon-copy fa fa-tags"></i>&nbsp; Create Wedding Order</div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="dashboard">Dashboard</a></li>
                                    <li class="breadcrumb-item"><a href="orders-admin">Orders Admin</a></li>
                                    <li class="breadcrumb-item"><a href="javascript:history.back()">back</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Add Additional Services</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="title">
                                    Order Detail
                                </div>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="title">
                                    Add Additional Services
                                </div>
                            </div>

                           
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    </div>
@endsection