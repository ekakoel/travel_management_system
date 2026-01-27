@section('title', __('messages.Activities Admin'))
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
                                    <i class="icon-copy dw dw-position" aria-hidden="true"></i> Activities
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item"><a href="/partners">Partners</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Activities</li>
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
                        @if (count($cactiveactivities)>0 or count($draftactivities)>0 or count($archiveactivities)>0)
                            <div class="col-md-4 mobile">
                                <div class="counter-container">
                                    @if (count($cactiveactivities)>0)
                                        <div class="widget">
                                            <a href="#activeactivities">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="micon dw dw-position" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $cactiveactivities->count() }} Activities</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($draftactivities)>0)
                                        <div class="widget">
                                            <a href="#draftactivities">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-draft">
                                                        <i class="micon dw dw-position" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $draftactivities->count() }} Activities</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($archiveactivities)>0)
                                        <div class="widget">
                                            <a href="#archiveactivities">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-archive">
                                                        <i class="micon dw dw-position" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $archiveactivities->count() }} Activities</div>
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
                            <div id="activeactivities" class="card-box">
                                <div class="card-box-title">
                                    <div class="title">Activities</div>
                                </div>
                                <div class="input-container">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchActivityByName" type="text" onkeyup="searchActivityByName()" class="form-control" name="search-activity-byname" placeholder="Search by Partner">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchActivityByLocation" type="text" onkeyup="searchActivityByLocation()" class="form-control" name="search-activity-location" placeholder="Search by location">
                                    </div>
                                </div>
                                @if (count($activeactivities)>0)
                                    <table id="tbActivities" class="data-table table stripe nowrap" >
                                        <thead>
                                            <tr>
                                                <th style="width: 5%;">No</th>
                                                <th style="width: 15%;">Partner</th>
                                                <th style="width: 15%;">Name</th>
                                                <th style="width: 5%;">Location</th>
                                                <th style="width: 5%;">Price/Pax</th>
                                                <th style="width: 5%;">Validity</th>
                                                <th style="width: 5%;">Status</th>
                                                <th style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($activeactivities as $no=>$activity)
                                            @php
                                                $usd_activity =ceil($activity->contract_rate / $usdrates->rate);
                                                $usd_activity_markup = $usd_activity + $activity->markup;
                                                $tax_activity = $taxes->tax / 100;
                                                $usd_activity_tax = ceil($usd_activity_markup * $tax_activity);
                                                $usd_activity_final = $usd_activity_markup + $usd_activity_tax;
                                            @endphp
                                                <tr>
                                                    <td>
                                                        <p>{!! ++$no !!}</p>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $partner = $partners->where('id', $activity->partners_id)->first();
                                                        @endphp
                                                        @if (isset($partner))
                                                            <div class="table-service-name">{!! $partner->name !!}</div>
                                                        @else
                                                            <div class="table-service-name">-</div>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        <div class="table-service-name">{!! $activity->name !!}</div>
                                                    </td>
                                                    <td>
                                                        <p>{!! $activity->location !!}</p>
                                                    </td>
                                                    <td>
                                                        <p>{!! currencyFormatUsd($usd_activity_final)." /pax" !!}</p>
                                                    </td>
                                                    <td>
                                                        <p>{{ dateFormat($activity->validity) }}</p>
                                                    </td>
                                                    <td>
                                                        @if ($activity->status == "Active")
                                                            <div class="status-active">Active</div>
                                                        @elseif ($activity->status == "Draft")
                                                            <div class="status-draft">Draft</div>
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="table-action">
                                                            <a href="/detail-activity-{{ $activity->id }}">
                                                                <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                                            </a>
                                                            @canany(['posDev','posAuthor'])
                                                                <a href="/edit-activity-{{ $activity->id }}">
                                                                    <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                                </a>
                                                                <form class="display-content" action="/remove-activity/{{ $activity->id }}" method="post">
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
                                        <div class="notification"><i class="icon-copy fa fa-info-circle" aria-hidden="true"></i> Tour packages are not yet available, please add some activity packages!</div>
                                    </div>
                                @endif
                                @canany(['posDev','posAuthor'])
                                    <div class="card-box-footer">
                                        <a href="/add-activity"><button class="btn btn-primary"><i class="ion-plus-round"></i> Add Activity</button></a>
                                    </div>
                                @endcanany
                            </div>
                        </div>
                        @if (count($cactiveactivities)>0 or count($draftactivities)>0 or count($archiveactivities)>0)
                            <div class="col-md-4 desktop">
                                <div class="row">
                                    @if (count($cactiveactivities)>0)
                                        <div class="col-xl-12 mb-18">
                                            <a href="#activeactivities">
                                                <div class="height-100-p widget-style1">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon">
                                                            <i class="micon dw dw-position" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $cactiveactivities->count() }} Activities</div>
                                                            <div class="widget-data-subtitle">Active</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($draftactivities)>0)
                                        <div class="col-xl-12 mb-18">
                                            <a href="#draftactivities">
                                                <div class="height-100-p widget-style1">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon">
                                                            <i class="micon dw dw-position" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $draftactivities->count() }} Activities</div>
                                                            <div class="widget-data-subtitle">Draft</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                    @endif
                                    @if (count($archiveactivities)>0)
                                        <div class="col-xl-12 mb-18">
                                            <a href="#archiveactivities">
                                                <div class="height-100-p widget-style1">
                                                    <div class="d-flex flex-wrap align-items-center">
                                                        <div class="chart-icon">
                                                            <i class="micon dw dw-position" aria-hidden="true"></i>
                                                        </div>
                                                        <div class="widget-data">
                                                            <div class="widget-data-title">{{ $archiveactivities->count() }} Activities</div>
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
                    @if (count($archiveactivities)>0)
                        <div id="archiveactivities" class="row">
                            <div class="col-md-8">
                                <div id="archiveactivities" class="card-box">
                                    <div class="card-box-title">
                                        <div class="title">Archived Activities</div>
                                    </div>
                                    <table class="data-table table nowrap" >
                                        <thead>
                                            <tr>
                                                <th style="width: 15%;">Name</th>
                                                <th style="width: 10%;">Status</th>
                                                <th style="width: 10%;">Location</th>
                                                <th style="width: 10%;">Duration</th>
                                                <th style="width: 10%;">Price/Pax</th>
                                                <th style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($archiveactivities as $activity)
                                            @php
                                                $usdrates = ceil($activity->contract_rate / $usdrates->rate);
                                            @endphp
                                                <tr>
                                                    <td>
                                                        <div class="table-service-name">{{ $activity['name'] }}</div>
                                                    </td>
                                                    <td>
                                                        @if ($activity->status == "Active")
                                                            <div class="status-active"></div>
                                                        @elseif ($activity->status == "Draft")
                                                            <div class="status-draft"></div>
                                                        @elseif ($activity->status == "Archived")
                                                            <div class="status-archived"></div>
                                                        @else
                                                        @endif
                                                    </td>
                                                    <td>
                                                        {{ $activity->location }}
                                                    </td>
                                                    <td>
                                                        {{ $activity->duration." Hours" }}
                                                    </td>
                                                    <td>
                                                        {{ "$ ".ceil(($usdrates * $activity->markup / 100) + $usdrates)." /pax" }}
                                                    </td>
                                                    <td>
                                                        <a href="/detail-activity-{{ $activity->id }}">
                                                            <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                                        </a>
                                                        <a href="/edit-activity-{{ $activity->id }}">
                                                            <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                        </a>
                                                        <form class="display-content" action="/remove-activity/{{ $activity->id }}" method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                            <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                        </form>
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
    function searchActivityByName() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchActivityByName");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbActivities");
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
    function searchActivityByLocation() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchActivityByLocation");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbActivities");
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
