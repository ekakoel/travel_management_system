@extends('layouts.head')
@section('title', __('messages.Tour Detail'))
@section('content')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title">
                            <i class="icon-copy dw dw-map-2" aria-hidden="true"></i> Tour Package
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                <li class="breadcrumb-item"><a href="/tours-admin">Tours Package</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $tour->name }}</li>
                            </ol>
                        </nav>
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
                    <div class="product-wrap">
                        <div class="product-detail-wrap">
                            <div class="row">
                                {{-- ATTENTIONS --}}
                                @if (count($attentions)>0)
                                    <div class="col-md-4 mobile">
                                        <div class="row">
                                            @include('admin.usd-rate')
                                            @include('layouts.attentions')
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-8">
                                    <div class="card-box m-b-18">
                                        <div class="card-box-title">
                                            <div class="subtitle">{{ $tour->name." - ".$tour->type?->type }}</div>
                                            <div class="status-card m-t-8">
                                                @if ($tour->status == "Rejected")
                                                    <div class="status-rejected">{{ $tour->status }}</div>
                                                @elseif ($tour->status == "Invalid")
                                                    <div class="status-invalid">{{ $tour->status }}</div>
                                                @elseif ($tour->status == "Active")
                                                    <div class="status-active">{{ $tour->status }}</div>
                                                @elseif ($tour->status == "Waiting")
                                                    <div class="status-waiting">{{ $tour->status }}</div>
                                                @elseif ($tour->status == "Draft")
                                                    <div class="status-draft">{{ $tour->status }}</div>
                                                @elseif ($tour->status == "Archived")
                                                    <div class="status-archived">{{ $tour->status }}</div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-box-body">
                                            <div class="cover-image m-b-18">
                                                <img src="{{ asset('storage/tours/tours-cover/'.$tour->cover) }}" alt="{{ $tour->name }}" loading="lazy">
                                            </div>
                                        </div>
                                        
                                        <div class="m-b-18">
                                            <div class="card-subtitle">{{ $tour->name }}</div>
                                            <p>
                                                {!! $tour->short_description !!}
                                            </p>
                                        </div>
                                        <div class="card-subtitle">Itinerary</div>
                                        <div class="m-b-18">
                                            {!! $tour->itinerary !!}
                                        </div>
                                        <div class="card-subtitle">Inclusions</div>
                                        <div class="m-b-18">
                                            {!! $tour->include !!}
                                        </div>
                                        <div class="card-subtitle">Exclusions</div>
                                        <div class="m-b-18">
                                            {!! $tour->exclude !!}
                                        </div>
                                        @if ($tour->additional_info)
                                            <div class="card-subtitle">Additional Information</div>
                                            <div class="m-b-18">
                                                {!! $tour->additional_info !!}
                                            </div>
                                        @endif
                                        @if ($tour->cancellation_policy)
                                            <div class="card-subtitle">Cancellation Policy</div>
                                            <div class="m-b-18">
                                                {!! $tour->cancellation_policy !!}
                                            </div>
                                        @endif
                                        <div class="card-subtitle">Tour Gallery</div>
                                        <div class="modal-galery">
                                            @if (count($tour->images)>0)
                                                @foreach ($tour->images as $tour_image)
                                                    <div class="gallery-item" id="image-{{ $tour_image->id }}">
                                                        {{-- <img src="{{ asset("storage/tours/tour-gallery/".$tour_image->image) }}" loading="lazy"> --}}
                                                        <img src="{{ getThumbnail("storage/tours/tour-gallery/".$tour_image->image,380,200) }}" class="thumbnail-image" loading="lazy">
                                                        @canany(['posDev','posAuthor'])
                                                            <div class="action-container">
                                                                <button class="action-remove" onclick="deleteImage({{ $tour_image->id }})">
                                                                    <i class="icon-copy dw dw-delete-3"></i>
                                                                </button>
                                                                <button class="action-update" onclick="updateImage({{ $tour_image->id }})">
                                                                    <i class="icon-copy dw dw-pencil"></i>
                                                                </button>
                                                            </div>
                                                        @endcanany
                                                    </div>
                                                @endforeach
                                            @else
                                                <p class="notification">No images available</p>
                                            @endif
                                        </div>
                                        @canany(['posDev','posAuthor'])
                                            @include('partials.modal-dropzone', compact("tour"))
                                        @endcanany
                                        <div class="card-box-footer">
                                            @canany(['posDev','posAuthor'])
                                                <a href="/edit-tour-{{ $tour['id'] }}"><button class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button></a>
                                            @endcanany
                                            <a href="/tours-admin"><button class="btn btn-secondary" ><i class="icon-copy fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                                        </div>
                                    </div>
                                    <div id="prices" class="card-box">
                                        <div class="card-box-title">
                                            <div class="title">Prices</div>
                                        </div>
                                        <div class="input-container">
                                            <div class="input-group">
                                                <span class="input-group-addon"><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                                <input id="searchPriceByCapacity" type="text" onkeyup="searchPriceByCapacity()" class="form-control" name="search-byroom" placeholder="Filter by Capacity">
                                            </div>
                                        </div>
                                        <table id="tbPrice" class="data-table table stripe nowrap" >
                                            <thead>
                                                <tr>
                                                    <th style="width: 5%;">#</th>
                                                    <th style="width: 20%;">Capacity</th>
                                                    <th style="width: 35%;">Expired Date</th>
                                                    <th style="width: 20%;">Public Rate / Pax</th>
                                                    <th style="width: 20%;">Status</th>
                                                    <th class="datatable-nosort text-center" style="width: 10%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($tour->prices as $price)
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>{{ $price->min_qty." - ". $price->max_qty." Guests" }}</td>
                                                        <td>{{ dateFormat($price->expired_date) }}</td>
                                                        {{-- <td>{{ currencyFormatUsd(ceil($price->contract_rate / $usdrates->rate)) }}</td>
                                                        <td>{{ currencyFormatUsd($price->markup) }}</td>
                                                        <td>{{ currencyFormatUsd(ceil(($price->calculated_price)*($tax->tax / 100))) }}</td> --}}
                                                        <td>{{ currencyFormatUsd($price->calculated_price) }}</td>
                                                        <td>
                                                            @if ($price->status == "Draft")
                                                                <div class="status-draft">{{ $price->status }}</div>
                                                            @else
                                                                <div class="status-active">{{ $price->status }}</div>
                                                            @endif
                                                        </td>
                                                        <td class="text-right">
                                                            <div class="table-action">
                                                                <a href="#" data-toggle="modal" data-target="#detail-price-{{ $price->id }}">
                                                                    <button class="btn-view"><i class="fa fa-eye"></i></button>
                                                                </a>
                                                                @canany(['posDev','posAuthor'])
                                                                    <a href="#" data-toggle="modal" data-target="#update-price-{{ $price->id }}">
                                                                        <button class="btn-edit"><i class="fa fa-edit"></i></button>
                                                                    </a>
                                                                    <form action="/fdelete-tour-price-{{ $price->id }}" method="post">
                                                                        @csrf
                                                                        @method('delete')
                                                                        <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                                    </form>
                                                                @endcan
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    {{-- MODAL PRICE DETAIL =========================================================================================================================================================--}}
                                                    <div class="modal fade" id="detail-price-{{ $price->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="title"><i class="dw dw-eye"></i> Price {{ $tour->name." | " .$price->min_qty." - ".$price->max_qty." guests" }}</div>
                                                                        <div class="status-card">
                                                                            @if ($price->status == "Draft")
                                                                                <div class="status-draft"></div>
                                                                            @else
                                                                                <div class="status-active"></div>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-12">
                                                                            <div class="row">
                                                                                <div class="col-12">
                                                                                    <div class="subtitle">USD Rate {{ ": ". number_format($usdrates->rate) }}</div>
                                                                                </div>
                                                                                <div class="col-12">
                                                                                    <hr class="form-hr">
                                                                                </div>
                                                                                <div class="col-12">
                                                                                    <div class="row">
                                                                                        <div class="col-2 m-b-8">
                                                                                            <p><b>Number of Guests :</b></p>
                                                                                            <p>{{ $price->min_qty." - ".$price->max_qty." guests" }}</p>
                                                                                        </div>
                                                                                        <div class="col-2  m-b-8">
                                                                                            <p><b>Contract Rate :</b></p>
                                                                                            <div class="rate-usd">{{ currencyFormatUsd(ceil($price->contract_rate / $usdrates->rate)) }}</div>
                                                                                        </div>
                                                                                        <div class="col-2  m-b-8">
                                                                                            <p><b>Markup :</b></p>
                                                                                            <div class="rate-usd">{{ currencyFormatUsd($price->markup) }}</div>
                                                                                        </div>
                                                                                        <div class="col-2  m-b-8">
                                                                                            <p><b>TAX :</b></p>
                                                                                            <div class="rate-usd">{{ currencyFormatUsd(ceil($price->calculated_price*$tax->tax / 100)) }}</div>
                                                                                        </div>
                                                                                        <div class="col-2  m-b-8">
                                                                                            <p><b>Price / Pax :</b></p>
                                                                                            <div class="rate-usd">{{ currencyFormatUsd($price->calculated_price) }}</div>
                                                                                        </div>
                                                                                        <div class="col-2 m-b-8">
                                                                                            <p><b>Expired Date :</b></p>
                                                                                            <p>{{ date('d M y', strtotime($price->expired_date)) }}</p>
                                                                                        </div>
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
                                                    {{-- MODAL EDIT PRICE =========================================================================================================================================================--}}
                                                    @canany(['posDev','posAuthor'])
                                                        <div class="modal fade" id="update-price-{{ $price->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="card-box text-left">
                                                                        <div class="card-box-title">
                                                                            <div class="title"><i class="dw dw-pencil"></i>Edit Price {{ $tour->name." | " .$price->min_qty." - ".$price->max_qty." guests" }}</div>
                                                                        </div>
                                                                        <form id="fedit-price-{{ $price->id }}" action="/fedit-tour-price-{{ $price->id }}" method="post" enctype="multipart/form-data">
                                                                            @method("PUT")
                                                                            {{ csrf_field() }}
                                                                            <div class="row">
                                                                                <div class="col-md-4 text-left">
                                                                                    <div class="form-group">
                                                                                        <label for="min_qty">Minimum Guests <span>*</span></label>
                                                                                        <div class="btn-icon">
                                                                                            <span><i class="icon-copy fa fa-users" aria-hidden="true"></i></span>
                                                                                            <input name="min_qty" type="number" min="1" id="min_qty" class="form-control @error('min_qty') is-invalid @enderror" placeholder="Minimum guests" type="text" value="{{ $price->min_qty }}" required>
                                                                                        </div>
                                                                                        @error('min_qty')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4 text-left">
                                                                                    <div class="form-group">
                                                                                        <label for="max_qty">Maximum Guests <span>*</span></label>
                                                                                        <div class="btn-icon">
                                                                                            <span><i class="icon-copy fa fa-users" aria-hidden="true"></i></span>
                                                                                            <input name="max_qty" type="number" min="1" id="max_qty" class="form-control  @error('max_qty') is-invalid @enderror" placeholder="Minimum guests" type="text" value="{{ $price->max_qty }}" required>
                                                                                        </div>
                                                                                        @error('max_qty')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4 text-left">
                                                                                    <div class="form-group">
                                                                                        <label for="status">Status <span>*</span></label>
                                                                                        <div class="btn-icon">
                                                                                            <span><i class="icon-copy dw dw-list"></i></span>
                                                                                            <select id="status" name="status" class="custom-select @error('status') is-invalid @enderror" required>
                                                                                                <option {{ $price->status == "Draft" ? 'selected' : '' }} value="Draft">Draft</option>
                                                                                                <option {{ $price->status == "Active" ? 'selected' : '' }} value="Active">Active</option>
                                                                                            </select>
                                                                                        </div>
                                                                                        @error('status')
                                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4 text-left">
                                                                                    <div class="form-group">
                                                                                        <label for="contract_rate">Contract Rate <span>*</span></label>
                                                                                        <div class="btn-icon">
                                                                                            <span>Rp</span>
                                                                                            <input name="contract_rate" type="number" min="1" id="contract_rate" class="form-control input-icon @error('contract_rate') is-invalid @enderror" placeholder="Contract rate" type="text" value="{{ $price->contract_rate }}" required>
                                                                                        </div>
                                                                                        @error('contract_rate')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4 text-left">
                                                                                    <div class="form-group">
                                                                                        <label for="markup">Markup <span>*</span></label>
                                                                                        <div class="btn-icon">
                                                                                            <span>$</span>
                                                                                            <input name="markup" type="number" min="1" id="markup" class="form-control input-icon  @error('markup') is-invalid @enderror" placeholder="insert markup" type="text" value="{{ $price->markup }}" required>
                                                                                        </div>
                                                                                        @error('markup')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-4">
                                                                                    <div class="form-group">
                                                                                        <label for="expired_date">Epired Date <span>*</span></label>
                                                                                        <div class="btn-icon">
                                                                                            <span><i class="icon-copy dw dw-calendar-11"></i></span>
                                                                                            <input name="expired_date" id="expired_date" wire:model="expired_date" class="form-control date-picker @error('expired_date') is-invalid @enderror" placeholder="Select Date and Time" type="text" value="{{ $price->expired_date }}" required>
                                                                                        </div>
                                                                                        @error('expired_date')
                                                                                            <span class="invalid-feedback">
                                                                                                <strong>{{ $message }}</strong>
                                                                                            </span>
                                                                                        @enderror
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <input type="hidden" name="tours_id" value="{{ $tour->id }}">
                                                                        </form>
                                                                        <div class="card-box-footer">
                                                                            <button type="submit" form="fedit-price-{{ $price->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endcan
                                                @endforeach
                                            </tbody>
                                        </table>
                                        @canany(['posDev','posAuthor'])
                                            <div class="card-box-footer">
                                                <a href="#" data-toggle="modal" data-target="#add-price">
                                                    <button class="button btn btn-primary" data-toggle="tooltip" data-placement="top" title="Add"><i class="icon-copy fa fa-plus"></i> Add Price</button>
                                                </a>
                                                {{-- MODAL ADD PRICE =========================================================================================================================================================--}}
                                                <div class="modal fade" id="add-price" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="card-box text-left">
                                                                <div class="card-box-title">
                                                                    <div class="title">
                                                                        <i class="fa fa-plus"></i>
                                                                        Add Price
                                                                    </div>
                                                                </div>
                                                                <form id="fadd-price-{{ $tour->id }}" action="/fadd-tour-price-{{ $tour->id }}" method="post" enctype="multipart/form-data">
                                                                    {{ csrf_field() }}
                                                                    <div class="row">
                                                                        <div class="col-md-4 text-left">
                                                                            <div class="form-group">
                                                                                <label for="min_qty">Minimum Guests <span>*</span></label>
                                                                                <div class="btn-icon">
                                                                                    <span><i class="icon-copy fa fa-users" aria-hidden="true"></i></span>
                                                                                    <input name="min_qty" type="number" min="1" id="min_qty" class="form-control  @error('min_qty') is-invalid @enderror" placeholder="Insert minimum guests" type="text" required>
                                                                                </div>
                                                                                @error('min_qty')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4 text-left">
                                                                            <div class="form-group">
                                                                                <label for="max_qty">Maximum Guests <span>*</span></label>
                                                                                <div class="btn-icon">
                                                                                    <span><i class="icon-copy fa fa-users" aria-hidden="true"></i></span>
                                                                                    <input name="max_qty" type="number" min="1" id="max_qty" class="form-control  @error('max_qty') is-invalid @enderror" placeholder="Insert maximum guests" type="text" required>
                                                                                </div>
                                                                                @error('max_qty')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4 text-left">
                                                                            <div class="form-group">
                                                                                <label for="contract_rate">Contract Rate / pax <span>*</span></label>
                                                                                <div class="btn-icon">
                                                                                    <span>Rp</span>
                                                                                    <input name="contract_rate" type="number" min="1" id="contract_rate" class="form-control  @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" type="text" required>
                                                                                </div>
                                                                                @error('contract_rate')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4 text-left">
                                                                            <div class="form-group">
                                                                                <label for="markup">Markup <span>*</span></label>
                                                                                <div class="btn-icon">
                                                                                    <span>
                                                                                        <i class="icon-copy fa fa-dollar" aria-hidden="true"></i>
                                                                                    </span>
                                                                                    <input name="markup" type="number" min="1" id="markup" class="form-control  @error('markup') is-invalid @enderror" placeholder="Insert markup" type="text" required>
                                                                                </div>
                                                                                @error('markup')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <div class="form-group">
                                                                                <label for="expired_date">Epired Date <span>*</span></label>
                                                                                <div class="btn-icon">
                                                                                    <span>
                                                                                        <i class="icon-copy dw dw-calendar-11"></i>
                                                                                    </span>
                                                                                    <input name="expired_date" id="expired_date" wire:model="expired_date" class="form-control date-picker @error('expired_date') is-invalid @enderror" placeholder="Select Date" type="text" required>
                                                                                </div>
                                                                                @error('expired_date')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <input type="hidden" name="tours_id" value="{{ $tour->id }}">
                                                                </form>
                                                                <div class="card-box-footer">
                                                                    <button type="submit" form="fadd-price-{{ $tour->id }}" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                    <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Close</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcan
                                    </div>
                                </div>
                                {{-- ATTENTIONS --}}
                                @if (count($attentions)>0)
                                    <div class="col-md-4 desktop">
                                        <div class="row">
                                            @include('admin.usd-rate')
                                            @include('layouts.attentions')
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endcan
@endsection

<script>
    function deleteImage(id) {
        if (!confirm('Are you sure you want to delete this image?')) return;

        $.ajax({
            url: "{{ url('/tours/gallery') }}/" + id,
            type: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    $("#image-" + id).fadeOut(300, function() {
                        $(this).remove();
                    });
                } else {
                    alert(response.message || 'Failed to delete image.');
                }
            },
            error: function(xhr) {
                alert('Server error: ' + xhr.responseText);
            }
        });
    }

    function updateImage(id) {
        // Buat input file secara dinamis
        let input = $('<input type="file" accept="image/*">');
        input.click();

        input.on('change', function() {
            let formData = new FormData();
            formData.append('file', this.files[0]);

            $.ajax({
                url: "{{ url('/tours/gallery') }}/" + id + "/update",
                type: 'POST',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $("#image-" + id + " img").attr('src', response.url + '?v=' + new Date().getTime());
                    } else {
                        alert(response.message || 'Failed to update image.');
                    }
                },
                error: function(xhr) {
                    alert('Server error: ' + xhr.responseText);
                }
            });
        });
    }
</script>

<script>
    function searchPriceByCapacity() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchPriceByCapacity");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbPrice");
        tr = table.getElementsByTagName("tr");
        for (i = 0; i < tr.length; i++) {
        td = tr[i].getElementsByTagName("td")[0];
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

