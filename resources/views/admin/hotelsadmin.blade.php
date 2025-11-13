@section('title', __('messages.Hotels'))
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
                                    <i class="icon-copy dw dw-hotel" aria-hidden="true"></i> Hotels
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Hotels</li>
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
                        @if (count($cactivehotels)>0 or count($drafthotels)>0 or count($archivehotels)>0)
                            <div class="col-md-4 mobile">
                                <div class="counter-container">
                                    @if (count($cactivehotels)>0)
                                        <div class="widget">
                                            <a href="#activehotels">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="micon dw dw-hotel" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $cactivehotels->count() }} Hotels</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($drafthotels)>0)
                                        <div class="widget">
                                            <a href="#drafthotels">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-draft">
                                                        <i class="micon dw dw-hotel" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $drafthotels->count() }} Hotels</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($archivehotels)>0)
                                        <div class="widget">
                                            <a href="#archivehotels">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-archive">
                                                        <i class="micon dw dw-hotel" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $archivehotels->count() }} Hotels</div>
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
                                    <div class="title">All Hotels</div>
                                </div>
                                <div class="input-container">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchHotelByName" type="text" onkeyup="searchHotelByName()" class="form-control" name="search-hotel-byname" placeholder="Search by name...">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchHotelByLocation" type="text" onkeyup="searchHotelByLocation()" class="form-control" name="search-hotel-location" placeholder="Search by location...">
                                    </div>
                                </div>
                                @if (count($hotels)>0)
                                    <table id="tbHotels" class="data-table table stripe hover nowrap">
                                        <thead>
                                            <tr>
                                                <th data-priority="1" class="datatable-nosort" style="width: 5%;">No</th>
                                                <th data-priority="1" style="width: 45%;">Name</th>
                                                <th data-priority="2" style="width: 15%;">Services</th>
                                                <th class="datatable-nosort" style="width: 15%;">Rooms</th>
                                                <th style="width: 10%;" class="datatable-nosort">Status</th>
                                                <th data-priority="1" class="datatable-nosort" style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($hotels as $no=>$hotel)
                                                @php
                                                    $n_price = $normal_prices->where('hotels_id',$hotel->id)->first();
                                                    $promo_max_date = $promos->where('hotels_id',$hotel->id)->first();
                                                    $package_max_date = $packages->where('hotels_id',$hotel->id)->first();
                                                @endphp
                                                <tr>
                                                    <td>
                                                        {{ ++$no }}
                                                    </td>
                                                    <td>
                                                        <div class="table-service-name">{{ $hotel['name'] }}</div>
                                                    </td>
                                                    <td>
                                                        <div class="indikator-service">
                                                            <p>
                                                                <span class="service {{ $n_price ? "service-active":"" }}" data-toggle="tooltip" data-placement="top" title="{{ $n_price ? date('d M y',strtotime($n_price->end_date)):"Normal Price" }}" aria-hidden="true">NP </span>|
                                                                <span class="service {{ $promo_max_date ? "service-active":"" }}" data-toggle="tooltip" data-placement="top" title="{{ $promo_max_date ? date('d M y',strtotime($promo_max_date->book_periode_end)):"Promo Price" }}" aria-hidden="true">PR </span>|
                                                                <span class="service {{ $package_max_date ? "service-active":"" }}" data-toggle="tooltip" data-placement="top" title="{{ $package_max_date ? date('d M y',strtotime($package_max_date->stay_period_end)):"Package Price" }}" aria-hidden="true">PA </span>
                                                            </p>
                                                            
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <p>{{ $hotel->rooms->where('status','Active')->count() }} A , {{ $hotel->rooms->where('status','Draft')->count() }} D </p>
                                                    </td>
                                                    <td>
                                                        @if ($hotel->status == "Active")
                                                            <div class="status-active inline-right">Active</div>
                                                        @else
                                                            <div class="status-draft inline-right">Draft</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="table-action">
                                                            <a href="/detail-hotel-{{ $hotel->id }}">
                                                                <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                                            </a>
                                                            @canany(['posDev','posAuthor'])
                                                                <a href="/edit-hotel-{{ $hotel->id }}">
                                                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                                </a>
                                                                <form class="display-content" action="/remove-hotel/{{ $hotel->id }}" method="post">
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
                                        <div class="notification"><i class="icon-copy fa fa-info-circle" aria-hidden="true"></i> Tour packages are not yet available, please add some hotel packages!</div>
                                    </div>
                                @endif
                                @canany(['posDev','posAuthor'])
                                    <div class="card-box-footer">
                                        <a href="/add-hotel"><button class="btn btn-primary"><i class="ion-plus-round"></i> Add Hotel</button></a>
                                    </div>
                                @endcanany
                            </div>
                        </div>
                        @if (count($cactivehotels)>0 or count($drafthotels)>0 or count($archivehotels)>0)
                            <div class="col-md-4 desktop">
                                <div class="counter-container">
                                    @if (count($cactivehotels)>0)
                                        <div class="widget">
                                            <a href="#activehotels">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="micon dw dw-hotel" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $cactivehotels->count() }} Hotels</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($drafthotels)>0)
                                        <div class="widget">
                                            <a href="#drafthotels">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-draft">
                                                        <i class="micon dw dw-hotel" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $drafthotels->count() }} Hotels</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($archivehotels)>0)
                                        <div class="widget">
                                            <a href="#archivehotels">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-archive">
                                                        <i class="micon dw dw-hotel" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $archivehotels->count() }} Hotels</div>
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
                    @if (count($archivehotels)>0)
                        <div id="archivehotels" class="row">
                            <div class="col-md-8">
                                <div id="archivehotels" class="card-box mb-30">
                                    <div class="row">
                                        <div class="col-md-12 col-sm-12">
                                            <div class="title">Archived Hotels</div>
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
                                            @foreach ($archivehotels as $hotel)
                                                <tr>
                                                    <td>
                                                        <div class="table-service-name">{{ $hotel['name'] }}</div>
                                                    </td>
                                                    <td>
                                                        <div class="status-archived"></div>
                                                    </td>
                                                    <td>
                                                        {{ $hotel->region }}
                                                    </td>
                                                    <td>
                                                        @if ($hotel->rooms->where('status','Active')->count() > 1)  
                                                            {{ $hotel->rooms->where('status','Active')->count() }} Rooms
                                                        @elseif ($hotel->rooms->where('status','Active')->count() == 1)  
                                                            {{ $hotel->rooms->where('status','Active')->count() }} Room
                                                        @else
                                                            0
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <a href="/detail-hotel-{{ $hotel['id'] }}" data-toggle="tooltip" data-placement="top" title="View">
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
@endsection
<script>
    function searchHotelByName() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchHotelByName");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbHotels");
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
    function searchHotelByLocation() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchHotelByLocation");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbHotels");
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