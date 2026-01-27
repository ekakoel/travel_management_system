@section('title', __('messages.Wedding Vendors'))
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
                                    <i class="icon-copy fi-torso-business"></i> Vendors
                                </div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Vendors</li>
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
                        @if (count($activevendors)>0 or count($draftvendors)>0)
                            <div class="col-md-4 mobile">
                               <div class="counter-container">
                                    @if (count($activevendors)>0)
                                        <a href="#activevendors">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="icon-copy fi-torso-business"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $activevendors->count() }} Vendors</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($draftvendors)>0)
                                        <a href="#draftvendors">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-draft">
                                                        <i class="icon-copy fi-torso-business"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $draftvendors->count() }} Vendors</div>
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
                        <div class="col-md-8">
                            <div class="card-box">
                                <div class="card-box-title">
                                    <div class="subtitle">Vendors</div>
                                </div>
                                <div class="input-container">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchVendorByName" type="text" onkeyup="searchVendorByName()" class="form-control" name="search-vendor-byname" placeholder="Search by name">
                                    </div>
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                        <input id="searchVendorByType" type="text" onkeyup="searchVendorByType()" class="form-control" name="search-vendor-location" placeholder="Search by type">
                                    </div>
                                </div>
                                @if (count($vendors)>0)
                                    <table id="tbVendors" class="data-table table stripe hover" >
                                        <thead>
                                            <tr>
                                                <th data-priority="1" class="datatable-nosort" style="width: 10%;">No</th>
                                                <th data-priority="2" style="width: 20%;">Name</th>
                                                <th style="width: 20%;">Type</th>
                                                <th class="datatable-nosort" style="width: 15%;">Services</th>
                                                <th style="width: 10%;">Status</th>
                                                <th class="datatable-nosort" style="width: 10%;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($vendors as $no=>$vendor)
                                                <tr>
                                                    <td>
                                                        {{ ++$no }}
                                                    </td>
                                                    <td>
                                                        <div class="table-service-name">{{ $vendor->name }}</div>
                                                    </td>
                                                    <td>
                                                        <p>{{ $vendor->type }}</p>
                                                    </td>
                                                    <td>
                                                        @php
                                                            $cpackages = $packages->where('vendor_id', $vendor->id);
                                                            $jml_packages = count($cpackages);
                                                        @endphp
                                                        <p>{{ $jml_packages." Services " }}</p>
                                                    </td>
                                                    <td>
                                                        @if ($vendor->status == "Active")
                                                            <div class="status-active"></div>
                                                        @elseif ($vendor->status == "Draft")
                                                            <div class="status-draft"></div>
                                                        @else
                                                        @endif
                                                    </td>
                                                    <td class="text-right">
                                                        <div class="table-action">
                                                            <a href="/detail-vendor-{{ $vendor->id }}">
                                                                <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                                            </a>
                                                            @canany(['posDev','weddingDvl','weddingAuthor'])
                                                                <form class="display-content" action="/fremove-vendor/{{ $vendor->id }}" method="post" enctype="multipart/form-data">
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
                                    <div class="notification-container">
                                        <div class="notification"><i class="icon-copy fa fa-info-circle" aria-hidden="true"></i> No Vendor in this page, add one!</div>
                                    </div>
                                @endif
                                @canany(['posDev','weddingDvl','weddingAuthor'])
                                    <div class="card-box-footer">
                                        <a href="#" data-toggle="modal" data-target="#add-vendor"><button class="btn btn-primary"><i class="ion-plus-round"></i> Add Vendor</button></a>
                                    </div>
                                    {{-- MODAL ADD VENDOR --------------------------------------------------------------------------------------------------------------- --}}
                                    <div class="modal fade" id="add-vendor" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <div class="subtitle"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Vendor</div>
                                                    </div>
                                                    <form id="addVendor" action="/fadd-vendor" method="post" enctype="multipart/form-data">
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
                                                                        <label for="name">Vendor Name</label>
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
                                                                            <option value="Decoration">Decoration</option>
                                                                            <option value="Documentation">Documentation</option>
                                                                            <option value="Entertainment">Entertainment</option>
                                                                            <option value="Hotel">Hotel</option>
                                                                            <option value="Make-up Artist">Make-up Artist</option>
                                                                            <option value="Event Organizer">Event Organizer</option>
                                                                            <option value="Other">Other</option>
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
                                                                        <label for="location">Location</label>
                                                                        <input type="text" id="location" name="location" class="form-control @error('location') is-invalid @enderror" placeholder="Location" value="{{ old('location') }}" required>
                                                                        @error('location')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-6 col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="contact_name">Contact Person</label>
                                                                        <input type="text" id="contact_name" name="contact_name" class="form-control @error('contact_name') is-invalid @enderror" placeholder="Name" value="{{ old('contact_name') }}" required>
                                                                        @error('contact_name')
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
                                                                        <label for="email">E-mail</label>
                                                                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="E-mail address" value="{{ old('email') }}" required>
                                                                        @error('email')
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
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="term">Terms and Conditions <span>*</span></label>
                                                                        <textarea name="term" id="term" wire:model="term" class="textarea_editor form-control @error('term') is-invalid @enderror" placeholder="Terms and Conditions" type="text">{!! old('term') !!}</textarea>
                                                                        @error('term')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="addVendor" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Create</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcanany
                            </div>
                        </div>
                        @if (count($activevendors)>0 or count($draftvendors)>0)
                            <div class="col-md-4 desktop">
                               <div class="counter-container">
                                    @if (count($activevendors)>0)
                                        <a href="#activevendors">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-active">
                                                        <i class="icon-copy fi-torso-business"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $activevendors->count() }} Vendors</div>
                                                        <div class="widget-data-subtitle">Active</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                    @if (count($draftvendors)>0)
                                        <a href="#draftvendors">
                                            <div class="widget">
                                                <div class="d-flex flex-wrap align-items-center">
                                                    <div class="chart-icon-draft">
                                                        <i class="icon-copy fi-torso-business"></i>
                                                    </div>
                                                    <div class="widget-data">
                                                        <div class="widget-data-title">{{ $draftvendors->count() }} Vendors</div>
                                                        <div class="widget-data-subtitle">Draft</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </a>
                                    @endif
                                </div>
                                @if ($attentions)
                                    @include('layouts.attentions')
                                @endif
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
    function searchVendorByName() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchVendorByName");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbVendors");
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
    function searchVendorByType() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchVendorByType");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbVendors");
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