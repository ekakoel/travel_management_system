@extends('layouts.head')
@section('title', __('messages.Detail SPK'))
@section('content')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="page-header">
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="title">
                            <i class="icon-copy dw dw-file-31"></i> SPK Detail
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('view.transport-management.index') }}">Surat Perintah Kerja (SPK)</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $spk->order_number }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <div class="card-box mb-4">
                        <div class="card-box-title">
                            <strong>{{ $spk->spk_number }}</strong>
                            <div class="status-badge badge {{ $bgStatus[$spk->status] ?? 'bg-secondary' }}">{{ $spk->status }}</div>
                        </div>
                        <div class="card-box-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="rd-list">
                                        <div class="rd-item">
                                            <div class="rd-label">Order Number</div>
                                            <div class="rd-value"><b>{{ $spk->order_number }}</b></div>
                                        </div>
                                        <div class="rd-item">
                                            <div class="rd-label">SPK Date</div>
                                            <div class="rd-value">{{ \Carbon\Carbon::parse($spk->spk_date)->locale('id')->translatedFormat('l, d M Y') }}</div>
                                        </div>
                                        <div class="rd-item">
                                            <div class="rd-label">Type</div>
                                            <div class="rd-value">{{ $spk->type ?? '-' }}</div>
                                        </div>
                                        
                                    </div>
                                </div>
                            
                                <div class="col-md-6">
                                    <div class="rd-list">
                                        <div class="rd-item">
                                            <div class="rd-label">Number of Guests</div>
                                            <div class="rd-value">{{ $spk->number_of_guests? $spk->number_of_guests." guests":"-" }}</div>
                                        </div>
                                        <div class="rd-item">
                                            <div class="rd-label">Vehicle</div>
                                            <div class="rd-value">{{ $spk->transport?->brand." ".$spk->transport?->name }}</div>
                                        </div>
                                        <div class="rd-item">
                                            <div class="rd-label">Driver</div>
                                            <div class="rd-value">{{ $spk->driver?->name }}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12"><hr></div>
                            </div>
                            <h5>
                                <strong>Guests</strong>
                            </h5>
                            <div class="m-b-18">
                                <table class="data-table table nowrap dataTable no-footer dtr-inline">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Name</th>
                                                <th>Sex / Age</th>
                                                <th>Contact</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($guests as $guest_no=>$guest)
                                                <tr>
                                                    <td>{{ ++$guest_no }}</td>
                                                    <td>{{ $guest->name }} {{ isset($guest->name_mandarin)?"(".$guest->name_mandarin.")":"";  }}</td>
                                                    <td>{{ $guest->sex == "m"?"Male":"Female"; }} / {{ $guest->age }}</td>
                                                    <td>{{ $guest->phone??"-" }}</td>
                                                    <td>
                                                        <form id="deleteGuest{{ $guest->id }}" action="{{ route('func.spk-guest.delete', $guest->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus guest ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                        </form>
                                                        <a href="#" data-toggle="modal" data-target="#editGuest{{ $guest->id }}">Edit</a>
                                                        |
                                                        <button type="submit" form="deleteGuest{{ $guest->id }}" class="btn-text">Delete</button>
                                                    </td>
                                                </tr>
                                                {{-- MODAL UPDATE GUEST --}}
                                                <div class="modal fade" id="editGuest{{ $guest->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="card-box">
                                                                <div class="card-box-title">
                                                                    <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update Guest</div>
                                                                </div>
                                                                <div class="card-box-body">
                                                                    <form id="updateGuest{{ $guest->id }}" action="{{ route('func.spk-guest.update',$guest->id) }}" method="POST" class="modal-content">
                                                                        @csrf
                                                                        <div class="alert alert-info" role="alert">
                                                                            <p>• Gunakan form ini untuk merubah data tamu <span><i>(Guest)</i></span> di dalam Surat Perintah Kerja (SPK).</p>
                                                                            <p>• Perubahan SPK akan memengaruhi dokumen resmi operasional bagi driver atau tim lapangan.</p>
                                                                            <p>• Pastikan data yang Anda perbarui sudah benar dan sesuai dengan reservasi terkait.</p>
                                                                        </div>
                                                                        <hr class="form-hr">
                                                                        <div class="row">
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="name">Name <span>*</span></label>
                                                                                    <div class="btn-icon">
                                                                                        <span><i class="icon-copy fa fa-user" aria-hidden="true"></i></span>
                                                                                        <input  name="name" class="form-control input-icon @error('name') is-invalid @enderror" type="text" value="{{ $guest->name }}" placeholder="Insert guest name" required>
                                                                                    </div>
                                                                                    @error('name')
                                                                                        <span class="invalid-feedback">
                                                                                            {{ $message }}
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="name_mandarin">Mandarin Name</label>
                                                                                    <div class="btn-icon">
                                                                                        <span><i class="icon-copy fa fa-user" aria-hidden="true"></i></span>
                                                                                        <input  name="name_mandarin" class="form-control input-icon @error('name_mandarin') is-invalid @enderror" type="text" value="{{ $guest->name_mandarin }}" placeholder="Insert guest Mandarin name">
                                                                                    </div>
                                                                                    @error('name_mandarin')
                                                                                        <span class="invalid-feedback">
                                                                                            {{ $message }}
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label>Sex <span>*</span></label>
                                                                                    <div class="btn-icon">
                                                                                        <span><i class="icon-copy fa fa-venus-mars" aria-hidden="true"></i></span>
                                                                                        <select name="sex" class="custom-select input-icon form-select" required>
                                                                                            <option disabled selected value="">Select Sex</option>
                                                                                            <option {{ $guest->sex == "m"?"selected":"" }} value="m">Male</option>
                                                                                            <option {{ $guest->sex == "f"?"selected":"" }} value="f">Female</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label>Age <span>*</span></label>
                                                                                    <div class="btn-icon">
                                                                                        <span><i class="icon-copy fa fa-male" aria-hidden="true"></i></span>
                                                                                        <select name="age" class="custom-select input-icon form-select" required>
                                                                                            <option disabled selected value="">Select Age</option>
                                                                                            <option {{ $guest->age == "Adult"?"selected":"" }} value="Adult">Adult</option>
                                                                                            <option {{ $guest->age == "Child"?"selected":"" }} value="Child">Child</option>
                                                                                        </select>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-md-6">
                                                                                <div class="form-group">
                                                                                    <label for="phone">Telephone</label>
                                                                                    <div class="btn-icon">
                                                                                        <span><i class="icon-copy fa fa-mobile-phone" aria-hidden="true"></i></span>
                                                                                        <input  name="phone" class="form-control input-icon @error('phone') is-invalid @enderror" type="number" value="{{ $guest->phone }}" placeholder="Insert telephone number">
                                                                                    </div>
                                                                                    @error('phone')
                                                                                        <span class="invalid-feedback">
                                                                                            {{ $message }}
                                                                                        </span>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <div class="card-box-footer">
                                                                    <button type="submit" form="updateGuest{{ $guest->id }}" class="btn btn-primary"><i class="icon-copy dw dw-diskette1"></i> Save</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy dw dw-cancel"></i> Cancel</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </tbody>
                                </table>
                                <div class="button-container">
                                    <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addGuest"><i class="fa fa-plus"></i> Add Guest</button>
                                </div>
                                {{-- MODAL ADD GUEST --}}
                                <div class="modal fade" id="addGuest" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="card-box">
                                                <div class="card-box-title">
                                                    <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add More Guest</div>
                                                </div>
                                                <div class="card-box-body">
                                                    <form id="addMoreGuest" action="{{ route('func.spk-guest.add',$spk->id) }}" method="POST" class="modal-content">
                                                        @csrf
                                                        <div class="alert alert-info" role="alert">
                                                            <p>• Gunakan form ini untuk menambahkan data tamu <span><i>(Guest)</i></span> ke dalam Surat Perintah Kerja (SPK).</p>
                                                            <p>• Perubahan SPK akan memengaruhi dokumen resmi operasional bagi driver atau tim lapangan.</p>
                                                            <p>• Pastikan data yang Anda perbarui sudah benar dan sesuai dengan reservasi terkait.</p>
                                                        </div>
                                                        <hr class="form-hr">
                                                        <div class="row">
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="name">Name <span>*</span></label>
                                                                    <div class="btn-icon">
                                                                        <span><i class="icon-copy fa fa-user" aria-hidden="true"></i></span>
                                                                        <input  name="name" class="form-control input-icon @error('name') is-invalid @enderror" type="text" value="{{ old('name') }}" placeholder="Insert guest name" required>
                                                                    </div>
                                                                    @error('name')
                                                                        <span class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="name_mandarin">Mandarin Name</label>
                                                                    <div class="btn-icon">
                                                                        <span><i class="icon-copy fa fa-user" aria-hidden="true"></i></span>
                                                                        <input  name="name_mandarin" class="form-control input-icon @error('name_mandarin') is-invalid @enderror" type="text" value="{{ old('name_mandarin') }}" placeholder="Insert guest Mandarin name">
                                                                    </div>
                                                                    @error('name_mandarin')
                                                                        <span class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Sex <span>*</span></label>
                                                                    <div class="btn-icon">
                                                                        <span><i class="icon-copy fa fa-venus-mars" aria-hidden="true"></i></span>
                                                                        <select name="sex" class="custom-select input-icon form-select" required>
                                                                            <option disabled selected value="">Select Sex</option>
                                                                            <option value="m">Male</option>
                                                                            <option value="f">Female</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label>Age <span>*</span></label>
                                                                    <div class="btn-icon">
                                                                        <span><i class="icon-copy fa fa-male" aria-hidden="true"></i></span>
                                                                        <select name="age" class="custom-select input-icon form-select" required>
                                                                            <option disabled selected value="">Select Age</option>
                                                                            <option value="Adult">Adult</option>
                                                                            <option value="Child">Child</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <div class="form-group">
                                                                    <label for="phone">Telephone</label>
                                                                    <div class="btn-icon">
                                                                        <span><i class="icon-copy fa fa-mobile-phone" aria-hidden="true"></i></span>
                                                                        <input  name="phone" class="form-control input-icon @error('phone') is-invalid @enderror" type="number" value="{{ old("phone") }}" placeholder="Insert telephone number">
                                                                    </div>
                                                                    @error('phone')
                                                                        <span class="invalid-feedback">
                                                                            {{ $message }}
                                                                        </span>
                                                                    @enderror
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                                <div class="card-box-footer">
                                                    <button type="submit" form="addMoreGuest" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy dw dw-cancel"></i> Cancel</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <h5>
                                <strong>Destinations</strong>
                            </h5>
                            @if($spk->destinations->count() > 0)
                                <table class="data-table table nowrap dataTable no-footer dtr-inline">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Destination Name</th>
                                            <th>Status</th>
                                            <th>Check-in At</th>
                                            <th>Check-in location</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($spk->destinations as $dest)
                                            <tr>
                                                <td>{{ date('l, d M Y (h:i A)',strtotime($dest->date)) }}</td>
                                                <td>
                                                    <a href="{{ $dest->destination_address }}" target="__blank" data-toggle="tooltip" data-placement="top" title="{{ $dest->description }}">
                                                        <i class="icon-copy fa fa-map-marker" aria-hidden="true"></i> {{ $dest->destination_name }}
                                                    </a>
                                                </td>
                                                <td>
                                                    @if($dest->status === 'Visited')
                                                        <span class="badge bg-success">Visited</span>
                                                    @else
                                                        <span class="badge bg-secondary">Pending</span>
                                                    @endif
                                                </td>
                                                <td>{{ $dest->visited_at ?? '-' }}</td>
                                                <td>
                                                    @if($dest->status === 'Visited')
                                                        <a href="{{ $dest->checkin_map_link }}" target="_blank" class="btn btn-sm btn-success color-white">
                                                            See on Map
                                                        </a>
                                                    @else
                                                        <em>Belum dikunjungi</em>
                                                    @endif
                                                </td>
                                                <td class="text-right pd-2-8">
                                                    <div class="table-action">
                                                        @if ($dest->status !== 'Visited')
                                                            <form id="deleteSpkDestination{{ $dest->id }}" action="{{ route('func.spk-destination.delete',$dest->id) }}" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                @method('delete')
                                                            </form>
                                                            <a href="#" data-toggle="modal" data-target="#updateSpkDestination-{{ $dest->id }}">
                                                                Edit
                                                            </a>
                                                            |
                                                            <button form="deleteSpkDestination{{ $dest->id }}" class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Remove">Delete</button>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                            {{-- MODAL EDIT DESTINATION --}}
                                            <div class="modal fade" id="updateSpkDestination-{{ $dest->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Destinasi</div>
                                                            </div>
                                                            <div class="card-box-body">
                                                                <form id="updateSpk{{ $dest->id }}" action="{{ route('func.spk-destinations.update',$dest->id) }}" method="POST" class="modal-content">
                                                                    @csrf
                                                                    <div class="alert alert-info" role="alert">
                                                                        <p>• Gunakan form ini untuk memperbarui data destinasi wisata yang sudah ada.</p>
                                                                        <p>• Perubahan data akan memengaruhi dokumen resmi operasional bagi driver atau tim lapangan.</p>
                                                                        <p>• Pastikan data yang Anda perbarui sudah benar dan sesuai dengan SPK terkait.</p>
                                                                    </div>
                                                                    <hr class="form-hr">
                                                                    <div class="row">
                                                                        <div class="col-md-6">
                                                                            <div class="form-group-icon">
                                                                                <label for="time">Time <span>*</span></label>
                                                                                <div class="btn-icon">
                                                                                    <span><i class="icon-copy dw dw-wall-clock1"></i></span>
                                                                                    <input type="time" name="time" class="form-control input-icon @error('time') is-invalid @enderror" placeholder="Select time" value="{{ date('H:i',strtotime($dest->date)) }}"  required>
                                                                                </div>
                                                                                @error('time')
                                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="form-group-icon">
                                                                                <label for="destinationName">Destination Name <span>*</span></label>
                                                                                <div class="btn-icon">
                                                                                    <span><i class="icon-copy dw dw-edit1"></i></span>
                                                                                    <input type="text" name="destination_name" class="form-control input-icon @error('destination_name') is-invalid @enderror" placeholder="Insert destination name" value="{{ $dest->destination_name }}" required>
                                                                                    @error('destination_name')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="form-group-icon">
                                                                                <label for="destinationAddress">Map Location</label>
                                                                                <div class="btn-icon">
                                                                                    <span><i class="icon-copy dw dw-map2"></i></span>
                                                                                    <input type="text" name="destination_address" class="form-control input-icon" placeholder="Copy url from Google Map" value="{{ $dest->destination_address }}">
                                                                                    @error('destination_address')
                                                                                        <div class="alert alert-danger">{{ $message }}</div>
                                                                                    @enderror
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <div class="form-group">
                                                                                <label for="description" class="form-label">Description</label>
                                                                                <textarea name="description" class="textarea_editor form-control" placeholder="Insert description">{{ $dest->description }}</textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="updateSpk{{ $dest->id }}" class="btn btn-primary"><i class="icon-copy dw dw-diskette1"></i> Save</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy dw dw-cancel"></i> Cancel</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">No destinations added yet.</p>
                            @endif
                            <div class="button-container">
                                <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#addDestination"><i class="fa fa-plus"></i> Add Destination</button>
                            </div>
                        </div>
                        <div class="card-box-footer">
                            <a href="https://api.whatsapp.com/send?text={{ urlencode('https://online.balikamitour.com/spk/'.$spk->id.'/'.$spk->spk_number) }}" 
                                target="_blank" 
                                class="btn btn-success">
                                <i class="bi bi-whatsapp"></i> Share SPK
                            </a>
                            <a href="{{ route('spks.print',$spk->id) }}" target="__blank">
                                <button class="btn btn-sm btn-light" data-toggle="modal" data-target="#changeDate"><i class="fa fa-print"></i> Print</button>
                            </a>
                            
                            <button class="btn btn-sm btn-primary" data-toggle="modal" data-target="#editSpkDetail"><i class="fa fa-pencil"></i> Edit SPK</button>
                            
                            <a href="{{ route('view.transport-management.index') }}">
                                <button class="btn btn-danger"><i class="icon-copy dw dw-left-arrow1"></i> Back</button>
                            </a>
                        </div>
                        {{-- MODAL EDIT SPK --}}
                        <div class="modal fade" id="editSpkDetail" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit SPK</div>
                                        </div>
                                        <div class="card-box-body">
                                            <form id="updateSpkDetail" action="{{ route('func.spk.update',$spk->id) }}" method="POST" class="modal-content">
                                                @csrf
                                                <div class="alert alert-info" role="alert">
                                                    <p>• Gunakan form ini untuk memperbarui data Surat Perintah Kerja (SPK) yang sudah ada.</p>
                                                    <p>• Perubahan SPK akan memengaruhi dokumen resmi operasional bagi driver atau tim lapangan.</p>
                                                    <p>• Pastikan data yang Anda perbarui sudah benar dan sesuai dengan reservasi terkait.</p>
                                                </div>
                                                <hr class="form-hr">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Order Number <span>*</span></label>
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy fa fa-qrcode" aria-hidden="true"></i></span>
                                                                <input
                                                                    class="form-control input-icon @error('order_number') is-invalid @enderror"
                                                                    name="order_number"
                                                                    type="text"
                                                                    value="{{ $spk->order_number }}"
                                                                    placeholder="@lang('messages.Insert order number')" 
                                                                    required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Status <span>*</span></label>
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy fa fa-check-square-o" aria-hidden="true"></i></span>
                                                                <select name="status" class="custom-select input-icon form-select" required>
                                                                    <option disabled selected value="">Select Status</option>
                                                                    <option {{ $spk->status == "Canceled"?"selected":"" }} value="Canceled">Cancelled</option>
                                                                    <option {{ $spk->status == "Pending"?"selected":"" }} value="Pending">Pending</option>
                                                                    <option {{ $spk->status == "In Progress"?"selected":"" }} value="In Progress">In Progress</option>
                                                                    <option {{ $spk->status == "Completed"?"selected":"" }} value="Completed">Completed</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Transport Service <span>*</span></label>
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy fa fa-server" aria-hidden="true"></i></span>
                                                                <select name="spk_type" class="custom-select input-icon form-select" required>
                                                                    <option disabled selected value="">Select Service</option>
                                                                    <option {{ $spk->type == "Airport Shuttle"?"selected":""; }} value="Airport Shuttle">Airport Shuttle</option>
                                                                    <option {{ $spk->type == "Hotel Transfer"?"selected":""; }} value="Hotel Transfer">Hotel Transfer</option>
                                                                    <option {{ $spk->type == "Tour"?"selected":""; }} value="Tour">Tour</option>
                                                                    <option {{ $spk->type == "Daily Rent"?"selected":""; }} value="Daily Rent">Daily Rent</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>SPK Date <span>*</span></label>
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy fa fa-calendar-check-o" aria-hidden="true"></i></span>
                                                                <input readonly
                                                                    class="form-control spk-date input-icon @error('spk_date') is-invalid @enderror"
                                                                    name="spk_date"
                                                                    type="date"
                                                                    value="{{ $spk->spk_date }}"
                                                                    placeholder="@lang('messages.Select date')" 
                                                                    required>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="number_of_guests">Number of Guests <span>*</span></label>
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy fa fa-users" aria-hidden="true"></i></span>
                                                                <input  name="number_of_guests" min="1" class="form-control input-icon @error('number_of_guests') is-invalid @enderror" type="number" value="{{ $spk->number_of_guests }}" placeholder="@lang('messages.Number of guests')" required>
                                                            </div>
                                                            @error('number_of_guests')
                                                                <span class="invalid-feedback">
                                                                    {{ $message }}
                                                                </span>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Vehicle <span>*</span></label>
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy fa fa-car" aria-hidden="true"></i></span>
                                                                <select name="transport_id" class="custom-select form-select" required>
                                                                    <option disabled selected value="">Select Vehicle</option>
                                                                    @foreach ($vehicles as $vehicle)
                                                                        <option {{ $spk->transport->id == $vehicle->id?"selected":""; }} value="{{ $vehicle->id }}">{{ $vehicle->brand." ".$vehicle->name }} {{ $vehicle->number_plate?" (".$vehicle->number_plate.")":"" }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label>Driver <span>*</span></label>
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy fa fa-user-circle-o" aria-hidden="true"></i></span>
                                                                <select name="driver_id" class="custom-select form-select" required>
                                                                    <option disabled selected value="">Select Driver</option>
                                                                    @foreach ($drivers as $driver)
                                                                        <option {{ $spk->driver->id == $driver->id?"selected":""; }} value="{{ $driver->id }}">{{ $driver->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="card-box-footer">
                                            <button type="submit" form="updateSpkDetail" class="btn btn-primary"><i class="icon-copy dw dw-diskette1"></i> Save</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy dw dw-cancel"></i> Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- MODAL ADD DESTINATION --}}
                        <div class="modal fade" id="addDestination" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Destination</div>
                                        </div>
                                        <div class="card-box-body">
                                            <form id="addSpkDestination" action="{{ route('func.spk-destinations.add',$spk->id) }}" method="POST" class="modal-content">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group-icon">
                                                            <label for="time">Time <span>*</span></label>
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy dw dw-wall-clock1"></i></span>
                                                                <input type="time" name="time" class="form-control input-icon @error('time') is-invalid @enderror" placeholder="Select time" value="{{ old('time') }}" required>
                                                            </div>
                                                            @error('time')
                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group-icon">
                                                            <label for="destinationName">Destination Name <span>*</span></label>
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy dw dw-edit1"></i></span>
                                                                <input type="text" id="destinationName" name="destination_name" class="form-control input-icon" placeholder="Insert destination name" value="{{ old('destination_name') }}" required>
                                                                @error('destination_name')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group-icon">
                                                            <label for="destinationAddress">Map Location</label>
                                                            <div class="btn-icon">
                                                                <span><i class="icon-copy dw dw-map2"></i></span>
                                                                <input type="text" id="destinationAddress" name="destination_address" class="form-control input-icon" placeholder="Copy url from Google Map">
                                                                @error('destination_name')
                                                                    <div class="alert alert-danger">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <div class="form-group">
                                                            <label for="description" class="form-label">Deskription</label>
                                                            <textarea name="description" class="textarea_editor form-control" placeholder="Insert description">{{ old('description') }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="card-box-footer">
                                            <button type="submit" form="addSpkDestination" class="btn btn-primary"><i class="icon-copy dw dw-add"></i> Add</button>
                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy dw dw-cancel"></i> Cancel</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card-box">
                        <div class="card-box-title">
                            <strong>Route on Map</strong>
                        </div>
                        <div class="card-box-body">
                            <p>Total Distance: <strong>{{ $spk->total_distance }} km</strong></p>
                            <div id="map" style="width: 100%; height: 500px; border-radius: 10px; margin-bottom:20px;"></div>
                            <ul>
                                @foreach($spk->destinations as $no => $dest)
                                    <li style="list-style: none; margin-left:0;"><p><span class="circle-number-{{ $dest->status }}">{{ $no+1 }}</span> {{ $dest->destination_name }} ({{ date('H:i',strtotime($dest->date)) }})</p></li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="card-box-footer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener("submit", function(e) {
            const form = e.target.closest("form");
            if (!form) return;

            // Cari tombol submit yang ada di dalam form
            let submitBtn = form.querySelector("[type=submit]");

            // Kalau nggak ada, coba cari tombol di luar form yang pakai atribut form="idForm"
            if (!submitBtn && form.id) {
                submitBtn = document.querySelector(`[type=submit][form="${form.id}"]`);
            }

            if (submitBtn) {
                // Disable tombol
                submitBtn.disabled = true;

                // Simpan teks asli
                const originalText = submitBtn.innerHTML;

                // Ganti dengan spinner
                submitBtn.innerHTML = `
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                `;

                // Kalau butuh restore (misalnya request AJAX gagal)
                form.addEventListener("ajaxError", function() {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                });
            }
        }, true);
    </script>
    <script>
        function initMap() {
            var destinations = {!! $destinationsJson !!};
            if (!destinations.length) return;

            var map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: destinations[0],
            });

            var directionsService = new google.maps.DirectionsService();
            var directionsRenderer = new google.maps.DirectionsRenderer({ map: map, suppressMarkers: true });

            var counts = {};
            destinations.forEach((d,i) => {
                var key = d.lat+','+d.lng;
                counts[key] = (counts[key]||0)+1;
                var pos = { lat: d.lat + 0.00005*(counts[key]-1), lng: d.lng + 0.00005*(counts[key]-1) };

                new google.maps.Marker({
                    position: pos,
                    map: map,
                    label: { text: (i+1).toString(), color:"white", fontSize:"14px", fontWeight:"bold" },
                    title: d.name + ' ('+d.status+')',
                    icon: { path: google.maps.SymbolPath.CIRCLE, scale:10, fillColor: d.status==='Visited'?'green':'grey', fillOpacity:1, strokeColor:'white', strokeWeight:2 }
                });
            });

            if (destinations.length>1) {
                var waypoints = destinations.slice(1,-1).map(d=>({location:{lat:d.lat,lng:d.lng}, stopover:true}));
                directionsService.route({
                    origin: {lat: destinations[0].lat,lng: destinations[0].lng},
                    destination: {lat: destinations[destinations.length-1].lat,lng: destinations[destinations.length-1].lng},
                    waypoints: waypoints,
                    travelMode: google.maps.TravelMode.DRIVING
                }, (res,status)=>{
                    if(status==='OK') directionsRenderer.setDirections(res);
                    else console.error('Gagal load route:',status);
                });
            }
        }

        window.initMap = initMap;
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=geometry,places&callback=initMap" async defer></script>
    @endcan
@endsection
