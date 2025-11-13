@extends('layouts.head')
@section('title', __('messages.Tour Package'))
@section('content')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="title">
                                <i class="icon-copy fa fa-briefcase"></i> Tour Package
                            </div>
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                    <li class="breadcrumb-item active">Tour Package</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>
                <div class="info-action">
                    @include('partials.alerts')
                </div>
                <div class="row">
                    @if ($totalTours > 0)
                        <div class="col-md-4 mobile">
                            <div class="counter-container">
                                @foreach(['Active' => $activetours, 'Draft' => $drafttours, 'Archived' => $archivetours] as $status => $collection)
                                    @if ($collection->count())
                                        <div class="widget">
                                            <a href="#{{ strtolower($status) }}tours">
                                                <div class="d-flex align-items-center">
                                                    <div class="chart-icon">
                                                        <i class="micon dw dw-map-2"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $collection->count() }} Tour Package</div>
                                                        <div class="widget-data-subtitle">{{ $status }}</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                    <div class="col-md-8">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="title">Tour Packages</div>
                            </div>
                            <div class="input-container">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                    <input id="searchTourByName" type="text" onkeyup="searchTourByName()" class="form-control" name="search-tour-byname" placeholder="Search by name...">
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                    <input id="searchTourByCode" type="text" onkeyup="searchTourByCode()" class="form-control" name="search-tour-code" placeholder="Search by code...">
                                </div>
                            </div>
                            @if ($activetours->count())
                                <table id="tbTours" class="data-table table stripe nowrap">
                                    <thead>
                                        <tr>
                                            <th>No</th>
                                            <th>Name</th>
                                            <th>Code</th>
                                            <th>Duration</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($activetours as $no => $tour)
                                            <tr>
                                                <td>{{ $no + 1 }}</td>
                                                <td>{{ $tour->name }}</td>
                                                <td>{{ $tour->code }}</td>
                                                <td>{{ $tour->duration }} {{ $tour->duration > 1 ? 'days':'day'; }}</td>
                                                <td><div class="status-{{ strtolower($tour->status) }}">{{ $tour->status }}</div></td>
                                                <td class="text-right">
                                                    <a href="/detail-tour-{{ $tour->id }}" class="btn-view"><i class="dw dw-eye"></i></a>
                                                    @canany(['posDev','posAuthor'])
                                                        <a href="/edit-tour-{{ $tour->id }}" class="btn-edit"><i class="fa fa-edit"></i></a>
                                                        <form action="/remove-tour/{{ $tour->id }}" method="post" class="d-inline-block" onsubmit="return confirm('Are you sure?');">
                                                            @csrf
                                                            @method('delete')
                                                            <button class="btn-delete" type="submit"><i class="fa fa-trash"></i></button>
                                                        </form>
                                                    @endcanany
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="notification"><i class="fa fa-info-circle"></i> No available tour packages.</div>
                            @endif
                            @canany(['posDev','posAuthor'])
                                <div class="card-box-footer">
                                    <a href="/add-tour" class="btn btn-primary"><i class="ion-plus-round"></i> Add Tour Package</a>
                                </div>
                            @endcanany
                        </div>
                    </div>
                    @if ($totalTours > 0)
                        <div class="col-md-4 desktop">
                            <div class="counter-container">
                                @foreach(['Active' => $activetours, 'Draft' => $drafttours, 'Archived' => $archivetours] as $status => $collection)
                                    @if ($collection->count())
                                        <div class="widget">
                                            <a href="#{{ strtolower($status) }}tours">
                                                <div class="d-flex align-items-center">
                                                    <div class="chart-icon">
                                                        <i class="micon dw dw-map-2"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $collection->count() }} Tour Package</div>
                                                        <div class="widget-data-subtitle">{{ $status }}</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endcan
    <script>
        function searchTourByName() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchTourByName");
            filter = input.value.toUpperCase();
            table = document.getElementById("tbTours");
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
        function searchTourByCode() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchTourByCode");
            filter = input.value.toUpperCase();
            table = document.getElementById("tbTours");
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
@endsection