@extends('layouts.head')
@section('title', __('messages.Itineraries'))
@section('content')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12 col-sm-12">
                                <div class="title">
                                    <i class="icon-copy dw dw-map-6" aria-hidden="true"></i> Itineraries
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item">Data</li>
                                        <li class="breadcrumb-item active" aria-current="page">Itineraries</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    @include('partials.alerts')
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="title">All Itineraries</div>
                                </div>
                                <div class="input-container">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchItineraryByName" type="text" onkeyup="searchItineraryByName()" class="form-control" name="search-itinerary-byname" placeholder="Search by title...">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchItineraryByCode" type="text" onkeyup="searchItineraryByCode()" class="form-control" name="search-itinerary-code" placeholder="Search by code...">
                                    </div>
                                </div>
                                @if (count($itineraries)>0)
                                    <table id="tbItineraries" class="data-table table stripe hover nowrap">
                                        <thead>
                                            <tr>
                                                <th data-priority="1" class="datatable-nosort" style="width: 5%;">#</th>
                                                <th data-priority="2" style="width: 25%;">Code</th>
                                                <th data-priority="1" style="width: 60%;">Title</th>
                                                <th data-priority="1" class="datatable-nosort" style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($itineraries as $no=>$itinerary)
                                                <tr>
                                                    <td>
                                                        {{ ++$no }}
                                                    </td>
                                                    <td>
                                                        <div class="table-service-name">{{ $itinerary->code }}</div>
                                                    </td>
                                                    <td>
                                                        <p>{!! $itinerary->title !!}</p>
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="table-action">
                                                            <a href="{{ route('itineraries.show',$itinerary->id) }}">
                                                                <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                                            </a>
                                                            @canany(['posDev','posAuthor'])
                                                                <a href="{{ route('itineraries.edit',$itinerary->id) }}">
                                                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                                </a>
                                                                <form class="display-content" action="{{ route('itineraries.destroy',$itinerary->id) }}" method="post">
                                                                    @csrf
                                                                    @method('delete')
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
                                        <div class="notification"><i class="icon-copy fa fa-info-circle" aria-hidden="true"></i> Itinerary are not yet available, please add one!</div>
                                    </div>
                                @endif
                                @canany(['posDev','posAuthor'])
                                    <div class="card-box-footer">
                                        <a href="{{ route('itineraries.create') }}"><button class="btn btn-primary"><i class="ion-plus-round"></i> Add Itinerary</button></a>
                                    </div>
                                @endcanany
                            </div>
                        </div>
                        {{-- @if (count($cactiveitineraries)>0 or count($draftitineraries)>0 or count($archiveitineraries)>0)
                            <div class="col-md-4 desktop">
                                <div class="counter-container">
                                    @if (count($cactiveitineraries)>0)
                                        <div class="widget">
                                            <a href="#activeitineraries">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="micon dw dw-itinerary" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $cactiveitineraries->count() }} Itineraries</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($draftitineraries)>0)
                                        <div class="widget">
                                            <a href="#draftitineraries">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-draft">
                                                        <i class="micon dw dw-itinerary" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $draftitineraries->count() }} Itineraries</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($archiveitineraries)>0)
                                        <div class="widget">
                                            <a href="#archiveitineraries">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-archive">
                                                        <i class="micon dw dw-itinerary" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $archiveitineraries->count() }} Itineraries</div>
                                                        <div class="widget-data-subtitle">Archived</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif --}}
                    </div>
                    {{-- @if (count($archiveitineraries)>0)
                        <div id="archiveitineraries" class="row">
                            <div class="col-md-8">
                                <div id="archiveitineraries" class="card-box mb-30">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12">
                                            <div class="title">Archived Itineraries</div>
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
                                            @foreach ($archiveitineraries as $itinerary)
                                                <tr>
                                                    <td>
                                                        <div class="table-service-name">{{ $itinerary['name'] }}</div>
                                                    </td>
                                                    <td>
                                                        <div class="status-archived"></div>
                                                    </td>
                                                    <td>
                                                        {{ $itinerary->region }}
                                                    </td>
                                                    <td>
                                                        @if ($itinerary->rooms->where('status','Active')->count() > 1)  
                                                            {{ $itinerary->rooms->where('status','Active')->count() }} Rooms
                                                        @elseif ($itinerary->rooms->where('status','Active')->count() == 1)  
                                                            {{ $itinerary->rooms->where('status','Active')->count() }} Room
                                                        @else
                                                            0
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="/detail-itinerary-{{ $itinerary['id'] }}" data-toggle="tooltip" data-placement="top" title="View">
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
                    @endif --}}
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
@endsection
<script>
    function searchItineraryByName() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchItineraryByName");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbItineraries");
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
    function searchItineraryByCode() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchItineraryByCode");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbItineraries");
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