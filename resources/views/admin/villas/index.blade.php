@section('title', __('messages.Private Villas'))
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
                                    <i class="icon-copy dw dw-building-1" aria-hidden="true"></i> Villas
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Villas</li>
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
                        @if (count($cactivevillas)>0 or count($draftvillas)>0 or count($archivevillas)>0)
                            <div class="col-md-4 mobile">
                                <div class="counter-container">
                                    @if (count($cactivevillas)>0)
                                        <div class="widget">
                                            <a href="#activevillas">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="micon dw dw-villa" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $cactivevillas->count() }} Villas</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($draftvillas)>0)
                                        <div class="widget">
                                            <a href="#draftvillas">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-draft">
                                                        <i class="micon dw dw-villa" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $draftvillas->count() }} Villas</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($archivevillas)>0)
                                        <div class="widget">
                                            <a href="#archivevillas">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-archive">
                                                        <i class="micon dw dw-villa" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $archivevillas->count() }} Villas</div>
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
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">All Villas</div>
                                </div>
                                <div class="input-container">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchVillaByName" type="text" onkeyup="searchVillaByName()" class="form-control" name="search-villa-byname" placeholder="Search by name...">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchVillaByLocation" type="text" onkeyup="searchVillaByLocation()" class="form-control" name="search-villa-location" placeholder="Search by location...">
                                    </div>
                                    <div class="input-group">
                                        <a href="/download-data-villa">
                                            <div class="btn btn-primary">
                                            <i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> Download PDF</i>
                                        </div>
                                        </a>
                                    </div>
                                </div>
                                @if (count($villas)>0)
                                    <table id="tbVillas" class="data-table table stripe hover nowrap">
                                        <thead>
                                            <tr>
                                                <th data-priority="1" class="datatable-nosort" style="width: 10%;">No</th>
                                                <th data-priority="2" style="width: 20%;">Name</th>
                                                <th style="width: 10%;">Stay Period Start</th>
                                                <th style="width: 10%;">Stay Period End</th>
                                                <th class="datatable-nosort" style="width: 10%;">Rooms</th>
                                                <th style="width: 10%;" class="datatable-nosort">Status</th>
                                                <th class="datatable-nosort" style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($villas as $no=>$villa)
                                                <tr>
                                                    <td>
                                                        {{ ++$no }}
                                                    </td>
                                                    <td>
                                                        <div class="table-service-name">{{ $villa['name'] }}</div>
                                                    </td>
                                                    <td>
                                                        @if ($villa->stay_period)
                                                            <p>{{ dateFormat($villa->stay_period->min_start_date) }}</p>
                                                        @else
                                                            <p style="color:red;">Expired</p>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if ($villa->stay_period)
                                                            <p>{{ dateFormat($villa->stay_period->max_end_date) }}</p>
                                                        @else
                                                            <p style="color:red;">Expired</p>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <p>{{ $villa->rooms->count() }}</p>
                                                    </td>
                                                    <td>
                                                        @if ($villa->status === "Active")
                                                            <div class="status-active inline-right">Active</div>
                                                        @else
                                                            <div class="status-draft inline-right">Draft</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="table-action">
                                                            <a href="{{ route('admin.villa.show',$villa->id) }}">
                                                                <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                                            </a>
                                                            @canany(['posDev','posAuthor'])
                                                                <a href="{{ route('admin.villa.edit',$villa->id) }}">
                                                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                                </a>
                                                            @endcanany
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="col-xl-12">
                                        <div class="notification"><i class="icon-copy fa fa-info-circle" aria-hidden="true"></i> Tour packages are not yet available, please add some villa packages!</div>
                                    </div>
                                @endif
                                @canany(['posDev','posAuthor'])
                                    <div class="card-box-footer">
                                        <a href="/add-villa"><button class="btn btn-primary"><i class="ion-plus-round"></i> Add Villa</button></a>
                                    </div>
                                @endcanany
                            </div>
                        </div>
                        @if (count($cactivevillas)>0 or count($draftvillas)>0 or count($archivevillas)>0)
                            <div class="col-md-4 desktop">
                                <div class="counter-container">
                                    @if (count($cactivevillas)>0)
                                        <div class="widget">
                                            <a href="#activevillas">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="micon dw dw-villa" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $cactivevillas->count() }} Villas</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($draftvillas)>0)
                                        <div class="widget">
                                            <a href="#draftvillas">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-draft">
                                                        <i class="micon dw dw-villa" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $draftvillas->count() }} Villas</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($archivevillas)>0)
                                        <div class="widget">
                                            <a href="#archivevillas">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-archive">
                                                        <i class="micon dw dw-villa" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $archivevillas->count() }} Villas</div>
                                                        <div class="widget-data-subtitle">Archived</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                    @if (count($archivevillas)>0)
                        <div id="archivevillas" class="row">
                            <div class="col-md-8">
                                <div id="archivevillas" class="card-box mb-30">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12">
                                            <div class="title">Archived Villas</div>
                                        </div>
                                    </div>
                                    <table class="data-table table nowrap" >
                                        <thead>
                                            <tr>
                                                <th style="width: 15%;">Name</th>
                                                <th style="width: 10%;">Status</th>
                                                <th style="width: 10%;">Location</th>
                                                <th style="width: 10%;">Room & Suite</th>
                                                <th style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($archivevillas as $villa)
                                                <tr>
                                                    <td>
                                                        <div class="table-service-name">{{ $villa['name'] }}</div>
                                                    </td>
                                                    <td>
                                                        <div class="status-archived"></div>
                                                    </td>
                                                    <td>
                                                        {{ $villa->region }}
                                                    </td>
                                                    <td>
                                                        @if ($villa->rooms->where('status','Active')->count() > 1)  
                                                            {{ $villa->rooms->where('status','Active')->count() }} Rooms
                                                        @elseif ($villa->rooms->where('status','Active')->count() == 1)  
                                                            {{ $villa->rooms->where('status','Active')->count() }} Room
                                                        @else
                                                            0
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="/detail-villa-{{ $villa['id'] }}" data-toggle="tooltip" data-placement="top" title="View">
                                                            <button class="btn-view"><i class="dw dw-eye"></i></button>
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
    <script>
        function searchVillaByName() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchVillaByName");
            filter = input.value.toUpperCase();
            table = document.getElementById("tbVillas");
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
        function searchVillaByLocation() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchVillaByLocation");
            filter = input.value.toUpperCase();
            table = document.getElementById("tbVillas");
            tr = table.getElementsByTagName("tr");
            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[3];
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
        $(document).ready(function() {
            $.ajax({
                url: "{{ route('villa.checkStatus') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    console.log('Status villa diperbarui:', response);
                },
                error: function(xhr) {
                    console.error('Gagal memperbarui status villa:', xhr.responseText);
                }
            });
        });
    </script>
@endsection