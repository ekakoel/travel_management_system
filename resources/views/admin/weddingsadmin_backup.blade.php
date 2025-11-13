@section('title', __('messages.Weddings'))
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
                                    @if ($service != "")
                                        {!! $service->icon !!} {{ $service->name }}
                                    @endif
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $service->name }}</li>
                                    </ol>
                                </nav>
                            </div>
                        </div>
                    </div>
                    <div class="info-action">
                        @if (\Session::has('error'))
                            <div class="alert alert-danger">
                                <ul>
                                    <li>{!! \Session::get('error') !!}</li>
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
                        <div class="col-md-4 mobile">
                            @if (count($activeweddings)>0 or count($draftweddings)>0 or count($archivedweddings)>0)
                                <div class="row">
                                    @if (count($activeweddings)>0)
                                        <div class="col-xl-12 mb-18">
                                            <a href="#activetours">
                                                <div class="height-100-p widget-style1">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon">
                                                            {!! $service->icon !!}
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $activeweddings->count() }} Weddings</div>
                                                            <div class="widget-data-subtitle">Active</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($draftweddings)>0)
                                    <div class="col-xl-12 mb-18">
                                        <a href="#draftweddings">
                                            <div class="height-100-p widget-style1">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon">
                                                        {!! $service->icon !!}
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $draftweddings->count() }} Weddings</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    @endif
                                    @if (count($archivedweddings)>0)
                                        <div class="col-xl-12 mb-18">
                                            <a href="#archivedweddings">
                                                <div class="height-100-p widget-style1">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon">
                                                            {!! $service->icon !!}
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="h4 mb-0">{{ $archivedweddings->count() }} Weddings</div>
                                                            <div class="weight-600 font-14">Archived</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <div class="row">
                                @include('layouts.attentions')
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div id="weddings" class="card-box m-b-18">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12">
                                        <div class="title">{{ $service->name }}</div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="input-group">
                                                <div class="col-md-4">
                                                    <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                                    <input id="searchWeddingByName" type="text" onkeyup="searchWeddingByName()" class="form-control" name="search-wedding-byname" placeholder="Search by name...">
                                                </div>
                                                <div class="col-md-4">
                                                    <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                                    <input id="searchWeddingByLocation" type="text" onkeyup="searchWeddingByLocation()" class="form-control" name="search-wedding-location" placeholder="Search by Hotel...">
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>
                                @if (count($weddings)>0)
                                    <table id="tbWeddings" class="data-table table nowrap" >
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">No</th>
                                                <th style="width: 10%;">Name</th>
                                                <th style="width: 10%;">Hotel</th>
                                                <th style="width: 10%;">File</th>
                                                <th style="width: 5%;">Status</th>
                                                <th style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($weddings as $no=>$wedding)
                                                @php
                                                    $hotel = $hotels->where('id',$wedding->hotel_id)->first();
                                                @endphp
                                                <tr>
                                                    <td>{{ ++$no }}<br>
                                                    </td>
                                                    <td>
                                                        <div class="table-service-name">{{ $wedding['name'] }}</div>
                                                    </td>
                                                    
                                                    <td>
                                                        @if ($hotel)
                                                            <p class="p-0 m-0">{{ $hotel->name }}</p>
                                                        @else
                                                            -
                                                        @endif
                                                    </td>
                                                   
                                                    <td>
                                                        <div class="property-icon">
                                                            <div class="icon-list" data-toggle="tooltip" data-placement="top" title="PDF">
                                                                <a href="#" data-target="#wedding-pdf-{{ $wedding->id }}" data-toggle="modal">
                                                                    <i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> 
                                                                </a>
                                                            </div>
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
                                                    </td>
                                                    <td>
                                                        @if ($wedding->status == "Active")
                                                            <div class="status-active"></div>
                                                        @elseif ($wedding->status == "Draft")
                                                            <div class="status-draft"></div>
                                                        @else
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="/{{ $service->nicname }}-admin-{{ $wedding['id'] }}" data-toggle="tooltip" data-placement="top" title="View">
                                                            <button class="btn-view"><i class="dw dw-eye"></i></button>
                                                        </a>
                                                        @canany(['posDev','posAuthor'])
                                                            @if ($wedding->status !== "Active")
                                                                <a href="/{{ $service->nicname }}-edit-{{ $wedding['id'] }}" data-toggle="tooltip" data-placement="top" title="Edit">
                                                                    <button class="btn-edit"><i class="icon-copy fa fa-edit"></i></button>
                                                                </a>
                                                                <form class="display-content" action="/f{{ $service->nicname }}-remove/{{ $wedding['id'] }}" method="post">
                                                                    <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete">
                                                                        <i class="icon-copy fa fa-trash"></i></button>
                                                                    @csrf
                                                                    @method('delete')
                                                                </form>
                                                            @endif
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="col-xl-12">
                                        <div class="notification"><i class="icon-copy fa fa-info-circle" aria-hidden="true"></i> Wedding packages are not yet available, please add some wedding packages!</div>
                                    </div>
                                @endif
                                @canany(['posDev','posAuthor'])
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12 text-right m-t-8 m-b-18">
                                            <a href="/{{ $service->nicname }}-add"><button class="btn btn-primary"><i class="ion-plus-round"></i> Add Wedding Package</button></a>
                                        </div>
                                    </div>
                                @endcan
                            </div>
                        </div>
                        <div class="col-md-4 desktop">
                            @if (count($activeweddings)>0 or count($draftweddings)>0 or count($archivedweddings)>0)
                                <div class="row">
                                    @if (count($activeweddings)>0)
                                        <div class="col-xl-12 mb-18">
                                            <a href="#activetours">
                                                <div class="height-100-p widget-style1">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon">
                                                            {!! $service->icon !!}
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $activeweddings->count() }} Weddings</div>
                                                            <div class="widget-data-subtitle">Active</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($draftweddings)>0)
                                    <div class="col-xl-12 mb-18">
                                        <a href="#draftweddings">
                                            <div class="height-100-p widget-style1">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon">
                                                        {!! $service->icon !!}
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $draftweddings->count() }} Weddings</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    @endif
                                    @if (count($archivedweddings)>0)
                                        <div class="col-xl-12 mb-18">
                                            <a href="#archivedweddings">
                                                <div class="height-100-p widget-style1">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon">
                                                            {!! $service->icon !!}
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="h4 mb-0">{{ $archivedweddings->count() }} Weddings</div>
                                                            <div class="weight-600 font-14">Archived</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                            <div class="row">
                                @include('layouts.attentions')
                            </div>
                        </div>
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
@endsection
<script>
    function searchWeddingByName() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchWeddingByName");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbWeddings");
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
    function searchWeddingByLocation() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchWeddingByLocation");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbWeddings");
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
