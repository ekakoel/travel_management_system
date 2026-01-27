@section('title', __('messages.Partners'))
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
                                    <i class="icon-copy fa fa-handshake-o" aria-hidden="true"></i> Partners
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Partners</li>
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
                        @if (\Session::has('invalid'))
                            <div class="alert alert-danger">
                                <ul>
                                    <li>{!! \Session::get('invalid') !!}</li>
                                </ul>
                            </div>
                        @endif
                    </div>
                    <div class="row">
                        @if (count($activepartners)>0 or count($draft_partners)>0)
                            <div class="col-md-4 mobile">
                               <div class="counter-container">
                                    @if (count($activepartners)>0)
                                        <a href="#activepartners">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="micon fa fa-handshake-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $activepartners->count() }} Partners</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($draft_partners)>0)
                                        <a href="#draftpartners">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-draft">
                                                        <i class="micon fa fa-handshake-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $draft_partners->count() }} Partners</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                            </div>
                            @include('layouts.attentions')
                        @endif
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle"><i class="icon-copy fa fa-handshake-o" aria-hidden="true"></i>Partners</div>
                                </div>
                                <div class="input-container">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchPartnerByName" type="text" onkeyup="searchPartnerByName()" class="form-control" name="search-partner-byname" placeholder="Search by name">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchPartnerByLocation" type="text" onkeyup="searchPartnerByLocation()" class="form-control" name="search-partner-location" placeholder="Search by location">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchPartnerByType" type="text" onkeyup="searchPartnerByType()" class="form-control" name="search-partner-location" placeholder="Search by type">
                                    </div>
                                </div>
                                @if (count($partners)>0)
                                    <table id="tbPartners" class="data-table table stripe hover" >
                                        <thead>
                                            <tr>
                                                <th data-priority="1" class="datatable-nosort" style="width: 10%;">No</th>
                                                <th data-priority="2" style="width: 20%;">Name</th>
                                                <th style="width: 20%;">Location</th>
                                                <th class="datatable-nosort" style="width: 15%;">Type</th>
                                                <th class="datatable-nosort" style="width: 15%;">Services</th>
                                                <th style="width: 10%;">Status</th>
                                                <th class="datatable-nosort" style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($partners as $no=>$partner)
                                                <tr>
                                                    <td>
                                                        {{ ++$no }}
                                                    </td>
                                                    <td>
                                                        <div class="table-service-name">{{ $partner->name }}</div>
                                                    </td>
                                                    <td>
                                                        <p>{{ $partner->location }}</p>
                                                    </td>
                                                    <td>
                                                        <p>{{ $partner->type }}</p>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $activity = $partner->activity->where('partners_id', $partner->id);
                                                            $tour = $partner->tours->where('partners_id', $partner->id);
                                                            $jml_activity = count($activity);
                                                            $jml_tour = count($tour);
                                                        @endphp
                                                        <p>{{ $jml_activity." A, ".$jml_tour." T" }}</p>
                                                    </td>
                                                    <td>
                                                        @if ($partner->status == "Active")
                                                            <div class="status-active"></div>
                                                        @elseif ($partner->status == "Draft")
                                                            <div class="status-draft"></div>
                                                        @else
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="table-action">
                                                            <a href="/detail-partner-{{ $partner->id }}">
                                                                <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                                            </a>
                                                            @canany(['posDev','posAuthor'])
                                                                <form class="display-content" action="/fremove-partner/{{ $partner->id }}" method="post" enctype="multipart/form-data">
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
                                        <div class="notification"><i class="icon-copy fa fa-info-circle" aria-hidden="true"></i> No Partner in this page, add one!</div>
                                    </div>
                                @endif
                                @canany(['posDev','posAuthor'])
                                    <div class="card-box-footer">
                                        <a href="#" data-toggle="modal" data-target="#add-partner"><button class="btn btn-primary"><i class="ion-plus-round"></i> Add Partner</button></a>
                                    </div>
                                    {{-- MODAL ADD PARTNER --------------------------------------------------------------------------------------------------------------- --}}
                                    <div class="modal fade" id="add-partner" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Partner</div>
                                                    </div>
                                                    <form id="addPartner" action="/fadd-partner" method="post" enctype="multipart/form-data">
                                                        @csrf
                                                        {{ csrf_field() }}
                                                        
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-12 col-sm-12 col-md-12">
                                                                    <div class="row">
                                                                        <div class="col-12 col-sm-6 col-md-6">
                                                                            <div class="card-subtitle m-b-8">Cover Image</div>
                                                                            <div class="dropzone text-center pd-20 m-b-18">
                                                                                <div class="cover-preview-div">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-6 col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="name">Cover Image</label>
                                                                        <input type="file" name="cover" id="cover" class="custom-file-input @error('cover') is-invalid @enderror" placeholder="Choose Cover" value="{{ old('cover') }}" required>
                                                                        @error('cover')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-6 col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="name">Partner Name</label>
                                                                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Name" value="{{ old('name') }}" required>
                                                                        @error('name')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group ">
                                                                        <label for="type">Type<span> *</span></label>
                                                                        <select name="type" id="type"  type="text" class="custom-select @error('type') is-invalid @enderror" placeholder="Select type" required>
                                                                            <option selected value="">Select type</option>
                                                                            <option value="Tourist Attraction">Tourist Attraction</option>
                                                                            <option value="Travel Agent">Travel Agent</option>
                                                                            <option value="Tour Agent">Tour Agent</option>
                                                                            <option value="Hotel">Hotel</option>
                                                                            <option value="Transport">Transport</option>
                                                                            <option value="Event Organizer">Event Organizer</option>
                                                                        </select>
                                                                        @error('type')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-6 col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="address">Address</label>
                                                                        <input type="text" id="address" name="address" class="form-control @error('address') is-invalid @enderror" placeholder="Address" value="{{ old('address') }}" required>
                                                                        @error('address')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-6 col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="contact_person">Contact Person</label>
                                                                        <input type="text" id="contact_person" name="contact_person" class="form-control @error('contact_person') is-invalid @enderror" placeholder="Name" value="{{ old('contact_person') }}" required>
                                                                        @error('contact_person')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-6 col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="phone">Phone</label>
                                                                        <input type="number" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Name" value="{{ old('phone') }}" required>
                                                                        @error('phone')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-6 col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="location">Location</label>
                                                                        <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" placeholder="Location" value="{{ old('location') }}" required>
                                                                        @error('location')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-6 col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="map">Map Link</label>
                                                                        <input type="text" id="map" name="map" class="form-control @error('map') is-invalid @enderror" placeholder="link" value="{{ old('map') }}" required>
                                                                        @error('map')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="description">Description</label>
                                                                        <textarea name="description" id="description" wire:model="description" class="textarea_editor form-control @error('description') is-invalid @enderror" placeholder="Description" type="text">{!! old('description') !!}</textarea>
                                                                        @error('description')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input id="author_id" name="author_id" value="{{ Auth::user()->id }}" type="hidden">
                                                    </form>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="addPartner" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcanany
                            </div>
                        </div>
                        @if (count($activepartners)>0 or count($draft_partners)>0)
                            <div class="col-md-4 desktop">
                               <div class="counter-container">
                                    @if (count($activepartners)>0)
                                        <a href="#activepartners">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="micon fa fa-handshake-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $activepartners->count() }} Partners</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($draft_partners)>0)
                                        <a href="#draftpartners">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-draft">
                                                        <i class="micon fa fa-handshake-o" aria-hidden="true"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $draft_partners->count() }} Partners</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                                @include('layouts.attentions')
                            </div>
                        @endif
                    </div>
                    @include('layouts.footer')
                </div>
            </div>
        </div>
    @endcan
@endsection
<script>
    function searchPartnerByName() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchPartnerByName");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbPartners");
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
    function searchPartnerByLocation() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchPartnerByLocation");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbPartners");
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
<script>
    function searchPartnerByType() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchPartnerByType");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbPartners");
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