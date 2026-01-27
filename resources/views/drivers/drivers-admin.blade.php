@section('title', __('messages.Driver'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @canany(['posDev','posAuthor','posRsv'])
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                <div class="page-header">
                    <div class="row">
                        <div class="col-md-12 col-sm-12">
                            <div class="title">
                                <i class="fa fa-user" aria-hidden="true"></i> Driver Manager
                            </div>
                            <nav aria-label="breadcrumb" role="navigation">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Driver Manager</li>
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
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 mobile">
                        @include('layouts.attentions')
                    </div>
                    <div class="col-md-8">
                        <div class="card-box">
                            <div class="card-box-title">
                                <div class="title">All Driver</div>
                            </div>
                            <div class="input-container">
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                    <input id="searchDriverByName" type="text" onkeyup="searchDriverByName()" class="form-control" name="search-driver-byname" placeholder="Search by name">
                                </div>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                    <input id="searchDriverByLicense" type="text" onkeyup="searchDriverByLicense()" class="form-control" name="search-driver-license" placeholder="Search by license">
                                </div>
                            </div>
                            <div class="table-container">
                               
                                    <table id="tbDrivers" class="data-table table stripe hover" >
                                        <thead>
                                            <tr>
                                                <th style="width: 10%">No</th>
                                                <th style="width: 20%">Name</th>
                                                <th style="width: 20%">Phone</th>
                                                <th style="width: 20%">License</th>
                                                <th style="width: 10%">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($drivers as $no=>$driver)
                                                @php 
                                                    $avg = $driver->averageRating();
                                                    $summary = $driver->reviewSummary();
                                                    $rating = 4.3;
                                                    $fullStars = floor($rating);
                                                    $halfStar = ($rating - $fullStars) >= 0.5;
                                                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                                @endphp
                                                <tr>
                                                    <td>
                                                        {{ ++$no }}
                                                    </td>
                                                    <td>
                                                        {{ $driver->name }}
                                                    </td>
                                                    <td>
                                                        {{ $driver->phone }}
                                                    </td>
                                                    <td>
                                                        <span class="me-2 fs-5 text-warning">
                                                            @for ($i = 0; $i < $fullStars; $i++)
                                                                <i class="icon-copy fa fa-star" aria-hidden="true"></i> 
                                                            @endfor
                                                            @if ($halfStar)
                                                                <i class="icon-copy fa fa-star-half-empty" aria-hidden="true"></i> 
                                                            @endif
                                                            @for ($i = 0; $i < $emptyStars; $i++)
                                                                <i class="icon-copy fa fa-star-o" aria-hidden="true"></i> 
                                                            @endfor
                                                        </span>
                                                        <span class="fw-semibold fs-5 text-primary">&nbsp;{{ number_format($summary['global_rating'], 1) }}</span>
                                                    </td>
                                                    <form id="destroy-driver-{{ $driver->id }}" action="/fdestroy-driver/{{ $driver->id }}" method="post">
                                                        @csrf
                                                        @method('delete')
                                                    </form>
                                                    <td>
                                                        <div class="table-action">
                                                            <a href="#" data-toggle="modal" data-target="#driver-view-{{ $driver->id }}">
                                                                <button class="btn-view"><i class="dw dw-eye"></i></button>
                                                            </a>
                                                            @canany(['posDev','posAuthor'])
                                                                <a href="#" data-toggle="modal" data-target="#driver-edit-{{ $driver->id }}">
                                                                    <button class="btn-edit"><i class="icon-copy fa fa-edit"></i></button>
                                                                </a>
                                                                <button type="submit" form="destroy-driver-{{ $driver->id }}" class="btn-delete" onclick="return confirm('Are you sure you want to remove the {{ $driver->name }} from the list of drivers?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash-o" aria-hidden="true"></i></button>
                                                            @endcan
                                                        </div>
                                                        {{-- MODAL VIEW DRIVERS DETAIL ----------------------------------------------------------------------------------------------------------- --}}
                                                        <div class="modal fade" id="driver-view-{{ $driver->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="title"><i class="icon-copy fa fa-user" aria-hidden="true"></i> Driver Detail</div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="row">
                                                                                <div class="col-md-3">
                                                                                    <div class="user-manager-img m-b-18">
                                                                                        <img src="{{ asset('storage/user/profile/default_user_img.png') }}" alt="{{ $driver->name }}">
                                                                                    </div>
                                                                                    <div class="rating-container text-center">
                                                                                        <h5>{{ $driver->name }}</h5>
                                                                                        <span class="me-2 fs-5 text-warning">
                                                                                            @for ($i = 0; $i < $fullStars; $i++)
                                                                                                <i class="icon-copy fa fa-star" aria-hidden="true"></i> 
                                                                                            @endfor
                                                                                            @if ($halfStar)
                                                                                                <i class="icon-copy fa fa-star-half-empty" aria-hidden="true"></i> 
                                                                                            @endif
                                                                                            @for ($i = 0; $i < $emptyStars; $i++)
                                                                                                <i class="icon-copy fa fa-star-o" aria-hidden="true"></i> 
                                                                                            @endfor
                                                                                        </span>
                                                                                        <span class="fw-semibold fs-5 text-primary">&nbsp;{{ number_format($summary['global_rating'], 1) }}</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-9">
                                                                                    <div class="row">
                                                                                        <div class="col-3 col-md-2">
                                                                                            <p>Name</p>
                                                                                            <p>Phone</p>
                                                                                            <p>Email</p>
                                                                                            <p>License</p>
                                                                                            <p>Address</p>
                                                                                            <p>Country</p>
                                                                                            <hr class="form-hr">
                                                                                            <p>Service</p>
                                                                                            <p>Transportation</p>
                                                                                        </div>
                                                                                        <div class="col-9 col-md-6">
                                                                                            <P>: {{ $driver->name }}</P>
                                                                                            <P>: {{ $driver->phone }}</P>
                                                                                            <P>: {{ $driver->email }}</P>
                                                                                            <P>: {{ $driver->license }}</P>
                                                                                            <P>: {{ $driver->address }}</P>
                                                                                            <P>: {{ $driver->country }}</P>
                                                                                            <hr class="form-hr">
                                                                                            <p>: {{ number_format($avg->driver, 1) }} ★</p>
                                                                                            <p>: {{ number_format($avg->transportation, 1) }} ★</p>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- MODAL EDIT DRIVERS ----------------------------------------------------------------------------------------------------------- --}}
                                                        @canany(['posDev','posAuthor'])
                                                            <div class="modal fade" id="driver-edit-{{ $driver->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                                    <div class="modal-content">
                                                                        <div class="card-box">
                                                                            <div class="card-box-title">
                                                                                <div class="title"><i class="fa fa-pencil"></i>Edit Driver</div>
                                                                            </div>
                                                                            <form id="update-driver-{{ $driver->id }}" action="/fedit-driver-{{ $driver->id }}" method="post" enctype="multipart/form-data">
                                                                                @csrf
                                                                                <div class="row">
                                                                                    <div class="col-md-12">
                                                                                        <div class="row">
                                                                                            <div class="col-md-4">
                                                                                                <div class="form-group">
                                                                                                    <label for="name" class="form-label">Name </label>
                                                                                                    <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert Driver Name" value="{{ $driver->name }}">
                                                                                                    @error('name')
                                                                                                        <span class="invalid-feedback">
                                                                                                            <strong>{{ $message }}</strong>
                                                                                                        </span>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-4">
                                                                                                <div class="form-group">
                                                                                                    <label for="phone" class="form-label">Telephone </label>
                                                                                                    <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Insert telephone number" value="{{ $driver->phone }}">
                                                                                                    @error('phone')
                                                                                                        <span class="invalid-feedback">
                                                                                                            <strong>{{ $message }}</strong>
                                                                                                        </span>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-4">
                                                                                                <div class="form-group">
                                                                                                    <label for="email" class="form-label">Email </label>
                                                                                                    <input type="text" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Insert Email" value="{{ $driver->email }}">
                                                                                                    @error('email')
                                                                                                        <span class="invalid-feedback">
                                                                                                            <strong>{{ $message }}</strong>
                                                                                                        </span>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-4">
                                                                                                <div class="form-group">
                                                                                                    <label for="license" class="form-label">License </label>
                                                                                                    <input type="text" id="license" name="license" class="form-control @error('license') is-invalid @enderror" placeholder="Insert Lecense" value="{{ $driver->license }}">
                                                                                                    @error('license')
                                                                                                        <span class="invalid-feedback">
                                                                                                            <strong>{{ $message }}</strong>
                                                                                                        </span>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-4">
                                                                                                <div class="form-group">
                                                                                                    <label for="country" class="form-label">Country </label>
                                                                                                    <input type="text" id="country" name="country" class="form-control @error('country') is-invalid @enderror" placeholder="Insert Country" value="{{ $driver->country }}">
                                                                                                    @error('country')
                                                                                                        <span class="invalid-feedback">
                                                                                                            <strong>{{ $message }}</strong>
                                                                                                        </span>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                            <div class="col-md-12">
                                                                                                <div class="form-group">
                                                                                                    <label for="address" class="form-label">Address </label>
                                                                                                    <textarea id="address" name="address" class="textarea_editor form-control @error('address') is-invalid @enderror" placeholder="Insert Address">{{ $driver->address }}</textarea>
                                                                                                    @error('address')
                                                                                                        <span class="invalid-feedback">
                                                                                                            <strong>{{ $message }}</strong>
                                                                                                        </span>
                                                                                                    @enderror
                                                                                                </div>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                    <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                                </div>
                                                                            </form>
                                                                            <div class="card-box-footer">
                                                                                <button type="submit" form="update-driver-{{ $driver->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Save</button>
                                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endcan
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                            </div>
                            @canany(['posDev','posAuthor','posRsv'])
                                <div class="card-box-footer">
                                    <a href="#" data-toggle="modal" data-target="#driver-add">
                                        <button class="btn btn-primary"><i class="icon-copy fa fa-plus"></i> Add Driver</button>
                                    </a>
                                </div>
                                {{-- MODAL ADD GUIDE ----------------------------------------------------------------------------------------------------------- --}}
                                <div class="modal fade" id="driver-add" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="title"><i class="fa fa-plus"></i>Add Driver</div>
                                                </div>
                                                <form id="add-driver" method="POST" action="{{ route('create-driver') }}">
                                                    @csrf
                                                    <div class="row">
                                                        <div class="col-md-12">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="name" class="form-label">Name </label>
                                                                        <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="Insert Driver Name" required>
                                                                        @error('name')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="phone" class="form-label">Telephone </label>
                                                                        <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="Insert telephone number" required>
                                                                        @error('phone')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="email" class="form-label">Email </label>
                                                                        <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="Insert Email" required>
                                                                        @error('email')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="license" class="form-label">License </label>
                                                                        <input type="license" id="license" name="license" class="form-control @error('license') is-invalid @enderror" placeholder="Insert License" required>
                                                                        @error('license')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="country" class="form-label">Country </label>
                                                                        <input type="text" id="country" name="country" class="form-control @error('country') is-invalid @enderror" placeholder="Insert Country" required>
                                                                        @error('country')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <div class="form-group">
                                                                        <label for="address" class="form-label">Address <span>*</span></label>
                                                                        <textarea id="address" name="address" class="textarea_editor form-control @error('address') is-invalid @enderror" placeholder="Insert Address"></textarea>
                                                                        @error('address')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                    </div>
                                                </form>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="add-driver" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Save</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endcan
                        </div>
                    </div>
                    {{-- ATTENTIONS --}}
                    <div class="col-md-4 desktop">
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
    function searchDriverByName() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchDriverByName");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbDrivers");
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
    function searchDriverByLicense() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchDriverByLicense");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbDrivers");
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