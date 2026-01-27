@section('title', __('messages.Transports'))
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
                                    <i class="icon-copy dw dw-bus" aria-hidden="true"></i> Transportation
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Transportation</li>
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
                    </div>
                    <div class="row">
                        @if (count($cactivetransports)>0 or count($drafttransports)>0 or count($archivetransports)>0)
                            <div class="col-md-4 mobile">
                                <div class="counter-container">
                                    @if (count($cactivetransports)>0)
                                        <div class="widget">
                                            <a href="#activetransports">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon">
                                                        <i class="micon dw dw-bus" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $cactivetransports->count() }} Transports</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($drafttransports)>0)
                                        <div class="widget">
                                            <a href="#drafttransports">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon">
                                                        <i class="micon dw dw-bus" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $drafttransports->count() }} Transports</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($archivetransports)>0)
                                        <div class="widget">
                                            <a href="#archivetransports">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon">
                                                        <i class="micon dw dw-bus" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $archivetransports->count() }} Transports</div>
                                                        <div class="widget-data-subtitle">Archived</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                        <div class="col-md-8">
                            <div id="activetransports" class="card-box">
                                <div class="card-box-title">
                                    <div class="title">Transportation</div>
                                </div>
                                <div class="input-container">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchTransportByName" type="text" onkeyup="searchTransportByName()" class="form-control" name="search-transport-byname" placeholder="Search by name...">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchTransportByType" type="text" onkeyup="searchTransportByType()" class="form-control" name="search-transport-type" placeholder="Search by type...">
                                    </div>
                                </div>
                                @if (count($activetransports)>0)
                                    <table id="tbTransports" class="data-table table stripe nowrap" >
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">No</th>
                                                <th style="width: 20%;">Name</th>
                                                <th style="width: 10%;">Type</th>
                                                <th style="width: 10%;">Capacity</th>
                                                <th style="width: 10%;">Status</th>
                                                <th style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($activetransports as $no=>$transport)
                                                <tr>
                                                    <td>
                                                        {{ ++$no }}
                                                    </td>
                                                    <td>
                                                        <div class="table-service-name">{{ $transport['name'] }}</div>
                                                    </td>
                                                    <td>
                                                        {{ $transport->type }}
                                                    </td>
                                                    <td>
                                                        {{ $transport->capacity." Seats" }}
                                                    </td>
                                                    <td>
                                                        @if ($transport->status == "Active")
                                                            <div class="status-active inline-right">Active</div>
                                                        @else
                                                            <div class="status-draft inline-right">Draft</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="table-action">
                                                            <a href="/detail-transport-{{ $transport->id }}">
                                                                <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                                            </a>
                                                            @canany(['posDev','posAuthor'])
                                                                <a href="/edit-transport-{{ $transport->id }}">
                                                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                                </a>
                                                                <form class="display-content" action="/delete-transport/{{ $transport->id }}" method="post">
                                                                    @csrf
                                                                    @method('put')
                                                                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                                </form>
                                                            @endcanany
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="col-xl-12">
                                        <div class="notification"><i class="icon-copy fa fa-info-circle" aria-hidden="true"></i> Tour packages are not yet available, please add some transport packages!</div>
                                    </div>
                                @endif
                                @canany(['posDev','posAuthor'])
                                    <div class="card-box-footer">
                                        <a href="/add-transport"><button class="btn btn-primary"><i class="ion-plus-round"></i> Add Transport</button></a>
                                    </div>
                                @endcanany
                            </div>
                        </div>

                        @if (count($cactivetransports)>0 or count($drafttransports)>0 or count($archivetransports)>0)
                            <div class="col-md-4 desktop">
                                <div class="row">
                                    @if (count($cactivetransports)>0)
                                        <div class="col-xl-12 mb-18">
                                            <a href="#activetransports">
                                                <div class="height-100-p widget-style1">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon">
                                                            <i class="micon dw dw-bus" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $cactivetransports->count() }} Transports</div>
                                                            <div class="widget-data-subtitle">Active</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($drafttransports)>0)
                                        <div class="col-xl-12 mb-18">
                                            <a href="#drafttransports">
                                                <div class="height-100-p widget-style1">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon">
                                                            <i class="micon dw dw-bus" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $drafttransports->count() }} Transports</div>
                                                            <div class="widget-data-subtitle">Draft</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($archivetransports)>0)
                                        <div class="col-xl-12 mb-18">
                                            <a href="#archivetransports">
                                                <div class="height-100-p widget-style1">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon">
                                                            <i class="micon dw dw-bus" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $archivetransports->count() }} Transports</div>
                                                            <div class="widget-data-subtitle">Archived</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    @if (count($archivetransports)>0)
                        <div id="archivetransports" class="row">
                            <div class="col-md-8">
                                <div id="archivetransports" class="card-box">
                                    <div class="card-box-title">
                                        <div class="title">Archived Transports</div>
                                    </div>
                                    <table id="tbTransports" class="data-table table nowrap" >
                                        <thead>
                                            <tr>
                                                <th style="width: 10%;">Name</th>
                                                <th style="width: 10%;">Status</th>
                                                <th style="width: 10%;">Type</th>
                                                <th style="width: 10%;">Capacity</th>
                                                <th style="width: 10%;">Price</th>
                                                <th style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($archivetransports as $transport)
                                                <tr>
                                                    <td>
                                                        <div class="table-service-name">{{ $transport['name'] }}</div>
                                                    </td>
                                                    <td>
                                                        @if ($transport->status == "Active")
                                                            <div class="status-active"></div>
                                                        @elseif ($transport->status == "Draft")
                                                            <div class="status-draft"></div>
                                                        @else
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $transport->type }}
                                                    </td>
                                                    <td>
                                                        {{ $transport->capacity." Seats" }}
                                                    </td>
                                                    <td>
                                                        {{ "$ ".$transport->price." /day" }}
                                                    </td>
                                                    <td>
                                                        <a href="/detail-transport-{{ $transport->id }}">
                                                            <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    @endif
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
@endsection
<script>
    function searchTransportByName() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchTransportByName");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbTransports");
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
    function searchTransportByType() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchTransportByType");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbTransports");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
            td = tr[i].getElementsByTagName("td")[2];
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
