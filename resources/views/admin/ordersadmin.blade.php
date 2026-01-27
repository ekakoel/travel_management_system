
@section('title', __('messages.Orders'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <i class="icon-copy fa fa-tags" aria-hidden="true"></i>All Orders
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item active" aria-current="page">Orders</li>
                                    </ol>
                                </nav>
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
                        @if (\Session::has('error_messages'))
                            <div class="alert alert-danger">
                                <ul>
                                    <li>{!! \Session::get('error_messages') !!}</li>
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        <div class="col-md-4 mobile">
                            <div class="counter-container">
                                @canany(['posDev','posAuthor','posRsv','weddingSls','weddingRsv','weddingDvl','weddingAuthor'])
                                    @canany(['posDev','posAuthor','posRsv'])
                                        @if (count($confirmedorders)>0)
                                            <a href="#confirmedorders">
                                                <div class="widget">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon-confirmed">
                                                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $confirmedorders->count() }} Tour Orders</div>
                                                            <div class="widget-data-subtitle">Confirmed</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                        @if (count($approvedorders)>0)
                                            <a href="#approvedorders">
                                                <div class="widget">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon-approved">
                                                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $approvedorders->count() }} Tour Orders</div>
                                                            <div class="widget-data-subtitle">Approved</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                        @if (count($activeorders)>0)
                                            <a href="#activeorders">
                                                <div class="widget">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon-active">
                                                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $activeorders->count() }} Tour Orders</div>
                                                            <div class="widget-data-subtitle">Active</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                        @if (count($waitingorders)>0)
                                            <a href="#pendingorders">
                                                <div class="widget">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon-pending">
                                                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $waitingorders->count() }} Tour Orders</div>
                                                            <div class="widget-data-subtitle">Pending</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                        @if (count($invalidorders)>0)
                                            <a href="#invalidorders">
                                                <div class="widget">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon-invalid">
                                                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $invalidorders->count() }} Tour Orders</div>
                                                            <div class="widget-data-subtitle">Invalid</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                        @if (count($rejectedorders)>0)
                                            <a href="#rejectedorders">
                                                <div class="widget">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon-rejected">
                                                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $rejectedorders->count() }} Tour Orders</div>
                                                            <div class="widget-data-subtitle">Rejected</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                    @endcanany
                                    @canany(['posDev','weddingSls','weddingRsv','weddingDvl','weddingAuthor'])
                                        @php
                                            $reject_w_orders = $orderWeddings->where('status','Rejected')->where('checkin','>=',$now);
                                            $invalid_w_orders = $orderWeddings->where('status','Invalid')->where('checkin','>=',$now);
                                            $active_w_orders = $orderWeddings->where('status','Active')->where('checkin','>=',$now);
                                            $confirm_w_orders = $orderWeddings->where('status','Confirm')->where('checkin','>=',$now);
                                            $pending_w_orders = $orderWeddings->where('status','Pending')->where('checkin','>=',$now);
                                            $Approve_w_orders = $orderWeddings->where('status','Approved')->where('checkin','>=',$now);
                                            $paid_w_orders = $orderWeddings->where('status','Paid')->where('checkin','>=',$now);
                                        @endphp
                                        @if (count($confirm_w_orders)>0)
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-confirmed">
                                                        <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $confirm_w_orders->count() }} Wedding Orders</div>
                                                        <div class="widget-data-subtitle">Confirmed</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (count($Approve_w_orders)>0)
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-approved">
                                                        <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $Approve_w_orders->count() }} Wedding Orders</div>
                                                        <div class="widget-data-subtitle">Approved</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (count($active_w_orders)>0)
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $active_w_orders->count() }} Wedding Orders</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (count($pending_w_orders)>0)
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-pending">
                                                        <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $pending_w_orders->count() }} Wedding Orders</div>
                                                        <div class="widget-data-subtitle">Pending</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (count($invalid_w_orders)>0)
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-invalid">
                                                        <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $invalid_w_orders->count() }} Wedding Orders</div>
                                                        <div class="widget-data-subtitle">Invalid</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (count($reject_w_orders)>0)
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-rejected">
                                                        <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $reject_w_orders->count() }} Wedding Orders</div>
                                                        <div class="widget-data-subtitle">Rejected</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endcanany
                                @endcanany
                            </div>
                            @include('layouts.attentions')
                        </div>
                        <div class="col-md-8">
                            @if (count($paidorders) > 0 or count($activeorders) > 0 or count($waitingorders) > 0 or count($invalidorders) > 0 or count($rejectedorders) > 0 or count($confirmedorders) > 0 or count($approvedorders)>0 or count($orderWeddings) >0)
                                @canany(['posDev','posAuthor','posRsv'])
                                    @if (count($paidorders) >0)
                                        @include('layouts.order-admin-paid')
                                    @endif
                                    @if (count($approvedorders) >0)
                                        @include('layouts.order-admin-approved')
                                    @endif
                                    @if (count($confirmedorders) >0)
                                        @include('layouts.order-admin-confirmed')
                                    @endif
                                    @if (count($activeorders) >0)
                                        @include('layouts.order-admin-active')
                                    @endif
                                    @if (count($waitingorders) >0)
                                        @include('layouts.order-admin-pending')
                                    @endif
                                    @if (count($invalidorders) >0)
                                        @include('layouts.order-admin-invalid')
                                    @endif
                                    @if (count($rejectedorders) >0)
                                        @include('layouts.order-admin-rejected')
                                    @endif
                                @endcanany
                                @canany(['posDev','weddingSls','weddingRsv','weddingDvl','weddingAuthor'])
                                    @if (count($orderWeddings) >0)
                                        @include('admin.order.order-wedding')
                                    @endif
                                @endcanany
                            @else
                                <div class="card-box">
                                    <div class="notif">No orders available</div>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-4 desktop">
                            <div class="counter-container">
                                @canany(['posDev','posAuthor','posRsv','weddingSls','weddingRsv','weddingDvl','weddingAuthor'])
                                    @canany(['posDev','posAuthor','posRsv'])
                                        @if (count($confirmedorders)>0)
                                            <a href="#confirmedorders">
                                                <div class="widget">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon-confirmed">
                                                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $confirmedorders->count() }} Tour Orders</div>
                                                            <div class="widget-data-subtitle">Confirmed</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                        @if (count($approvedorders)>0)
                                            <a href="#approvedorders">
                                                <div class="widget">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon-approved">
                                                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $approvedorders->count() }} Tour Orders</div>
                                                            <div class="widget-data-subtitle">Approved</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                        @if (count($activeorders)>0)
                                            <a href="#activeorders">
                                                <div class="widget">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon-active">
                                                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $activeorders->count() }} Tour Orders</div>
                                                            <div class="widget-data-subtitle">Active</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                        @if (count($waitingorders)>0)
                                            <a href="#pendingorders">
                                                <div class="widget">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon-pending">
                                                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $waitingorders->count() }} Tour Orders</div>
                                                            <div class="widget-data-subtitle">Pending</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                        @if (count($invalidorders)>0)
                                            <a href="#invalidorders">
                                                <div class="widget">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon-invalid">
                                                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $invalidorders->count() }} Tour Orders</div>
                                                            <div class="widget-data-subtitle">Invalid</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                        @if (count($rejectedorders)>0)
                                            <a href="#rejectedorders">
                                                <div class="widget">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon-rejected">
                                                            <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $rejectedorders->count() }} Tour Orders</div>
                                                            <div class="widget-data-subtitle">Rejected</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        @endif
                                    @endcanany
                                    @canany(['posDev','weddingSls','weddingRsv','weddingDvl','weddingAuthor'])
                                        @php
                                            $reject_w_orders = $orderWeddings->where('status','Rejected')->where('checkin','>=',$now);
                                            $invalid_w_orders = $orderWeddings->where('status','Invalid')->where('checkin','>=',$now);
                                            $active_w_orders = $orderWeddings->where('status','Active')->where('checkin','>=',$now);
                                            $confirm_w_orders = $orderWeddings->where('status','Confirm')->where('checkin','>=',$now);
                                            $pending_w_orders = $orderWeddings->where('status','Pending')->where('checkin','>=',$now);
                                            $Approve_w_orders = $orderWeddings->where('status','Approved')->where('checkin','>=',$now);
                                            $paid_w_orders = $orderWeddings->where('status','Paid')->where('checkin','>=',$now);
                                        @endphp
                                        @if (count($confirm_w_orders)>0)
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-confirmed">
                                                        <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $confirm_w_orders->count() }} Wedding Orders</div>
                                                        <div class="widget-data-subtitle">Confirmed</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (count($Approve_w_orders)>0)
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-approved">
                                                        <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $Approve_w_orders->count() }} Wedding Orders</div>
                                                        <div class="widget-data-subtitle">Approved</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (count($active_w_orders)>0)
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $active_w_orders->count() }} Wedding Orders</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (count($pending_w_orders)>0)
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-pending">
                                                        <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $pending_w_orders->count() }} Wedding Orders</div>
                                                        <div class="widget-data-subtitle">Pending</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (count($invalid_w_orders)>0)
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-invalid">
                                                        <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $invalid_w_orders->count() }} Wedding Orders</div>
                                                        <div class="widget-data-subtitle">Invalid</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                        @if (count($reject_w_orders)>0)
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-rejected">
                                                        <i class="icon-copy fa fa-tags" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $reject_w_orders->count() }} Wedding Orders</div>
                                                        <div class="widget-data-subtitle">Rejected</div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endcanany
                                @endcanany
                            </div>
                            @include('layouts.attentions')
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
@endsection

<script>
    function searchApprovedOrderByTyp() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchApprovedOrderByTyp");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbApprovedOrders");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
</script>
<script>
    function searchApprovedOrderByAgn() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchApprovedOrderByAgn");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbApprovedOrders");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
</script>
<script>
    function searchActiveOrderByAgn() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchActiveOrderByAgn");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbActiveOrders");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
</script>
<script>
    function searchActiveOrderByType() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchActiveOrderByType");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbActiveOrders");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
</script>
<script>
    function searchWaitingOrdersByAgn() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchWaitingOrdersByAgn");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbWaitingOrders");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[0];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
</script>
<script>
    function searchWaitingOrderByType() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchWaitingOrderByType");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbWaitingOrders");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[1];
            if (td) {
                txtValue = td.textContent || td.innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    tr[i].style.display = "";
                } else {
                    tr[i].style.display = "none";
                }
            }       
        }
    }
</script>
