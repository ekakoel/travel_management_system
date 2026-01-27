@section('title', __('messages.Transport Detail'))
@section('content')
    @extends('layouts.head')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="title">
                            <i class="icon-copy fa fa-car" aria-hidden="true"></i> Transportation
                        </div>
                        <nav aria-label="breadcrumb" role="navigation">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                <li class="breadcrumb-item"><a href="/transports-admin">Transportations</a></li>
                                <li class="breadcrumb-item active" aria-current="page">{{ $transport->name }}</li>
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
                                <div class="col-md-4 mobile">
                                    <div class="row">
                                        @include('admin.usd-rate')
                                        @include('layouts.attentions')
                                    </div>
                                </div>
                                <div class="col-md-8">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title">{{ $transport->name }}</div>
                                            <div class="status-card">
                                                @if ($transport->status == "Rejected")
                                                    <div class="status-rejected"></div>
                                                @elseif ($transport->status == "Invalid")
                                                    <div class="status-invalid"></div>
                                                @elseif ($transport->status == "Active")
                                                    <div class="status-active"></div>
                                                @elseif ($transport->status == "Waiting")
                                                    <div class="status-waiting"></div>
                                                @elseif ($transport->status == "Draft")
                                                    <div class="status-draft"></div>
                                                @elseif ($transport->status == "Archived")
                                                    <div class="status-archived"></div>
                                                @else
                                                @endif
                                            </div>
                                        </div>
                                        <div class="page-card">
                                            <figure class="card-banner-transport">
                                                <img src="{{ asset ('storage/transports/transports-cover/' . $transport->cover) }}" alt="{{ $transport->name }}" loading="lazy">
                                            </figure>
                                            <div class="card-content">
                                                <div class="card-text">
                                                    <div class="row ">
                                                        <div class="col-4">
                                                            <div class="card-subtitle">Capacity:</div>
                                                            <p>{{ $transport->capacity." Seats" }}</p>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="card-subtitle">Include:</div>
                                                            <p>{!! $transport->include!!}</p>
                                                        </div>
                                                        <div class="col-4">
                                                            <div class="card-subtitle">Type:</div>
                                                            <p>{{  $transport->type  }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                @if ($transport->additional_info != "")
                                                    <hr class="form-hr">
                                                    <div class="card-text">
                                                        <div class="row ">
                                                            <div class="col-md-12">
                                                                <div class="card-subtitle">Additional Information:</div>
                                                                <p>{!! $transport->additional_info !!}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                                @if ($transport->cancellation_policy != "")
                                                    <hr class="form-hr">
                                                    <div class="card-text">
                                                        <div class="row">
                                                            <div class="col-md-12">
                                                                <div class="card-subtitle">Cancellation Policy:</div>
                                                                <p>{!! $transport->cancellation_policy !!}</p>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="card-box-footer">
                                            @canany(['posDev','posAuthor'])
                                                <a href="/edit-transport-{{ $transport->id }}"><button class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button></a>
                                            @endcanany
                                            <a href="/transports-admin"><button class="btn btn-secondary" ><i class="icon-copy fa fa-arrow-left" aria-hidden="true"></i> Back</button></a>
                                        </div>
                                    </div>
                                </div>
                                {{-- ATTENTIONS --}}
                                <div class="col-md-4 desktop">
                                    <div class="row">
                                        @include('admin.usd-rate')
                                        @include('layouts.attentions')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="prices" class="product-wrap">
                        <div class="product-detail-wrap">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="title">Prices</div>
                                        </div>
                                        <div class="input-container">
                                            <div class="input-group">
                                                <span><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                                <input id="searchPriceByType" type="text" onkeyup="searchPriceByType()" class="form-control" name="search-price-by-type" placeholder="Filter by type">
                                            </div>
                                            <div class="input-group">
                                                <span><i class="icon-copy fa fa-search" aria-hidden="true"></i></span>
                                                <input id="searchPriceByDuration" type="text" onkeyup="searchPriceByDuration()" class="form-control" name="search-price-by-duration" placeholder="Filter by duration">
                                            </div>
                                        </div>
                                        <table id="tbPrice" class="data-table table stripe nowrap" >
                                            <thead>
                                                <tr>
                                                    <th style="width: 10%;">Type</th>
                                                    <th style="width: 5%;">Contract Rate</th>
                                                    <th style="width: 5%;">Markup</th>
                                                    <th style="width: 5%;">Tax</th>
                                                    <th style="width: 5%;">Published Rate</th>
                                                    <th style="width: 5%;">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                               @foreach($prices as $prices)
                                               @php
                                                    $usd_transport = ceil($prices->contract_rate / $usdrates->rate);
                                                    $transport_markup =  $usd_transport + $prices->markup;
                                                    $tax = $taxes->tax / 100;
                                                    $transport_tax = ceil($transport_markup * $tax);
                                                    $transport_final_price = $transport_markup + $transport_tax;
                                               @endphp
                                                    <tr>
                                                        <td>
                                                            <p>{{ $prices->type }}</p>
                                                            <p>{{ $prices->duration." Hours" }}</p>
                                                            @if ($prices->type == "Airport Shuttle")
                                                                <div class="subtext">
                                                                    <span>src: </span>{{ $prices->src }}
                                                                </div>
                                                                <div class="subtext">
                                                                    <span>dst: </span>{{ $prices->dst }}
                                                                </div>
                                                            @elseif ($prices->type == "Transfers")
                                                                <div class="subtext">
                                                                    <span>src: </span>{{ $prices->src }}
                                                                </div>
                                                                <div class="subtext">
                                                                    <span>dst: </span>{{ $prices->dst }}
                                                                </div>
                                                            @elseif ($prices->type == "Daily Rent")
                                                                @if ($prices->duration <= 6)
                                                                    <div class="subtext">
                                                                        Half Day
                                                                    </div>
                                                                @else
                                                                    <div class="subtext">
                                                                        Full Day
                                                                    </div>
                                                                @endif
                                                            @else
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="rate-usd">{{ currencyFormatUsd($usd_transport) }}</div>
                                                            <div class="rate-idr">{{ currencyFormatIdr($prices->contract_rate) }}</div>
                                                        </td>
                                                        <td>
                                                            <div class="rate-usd">{{ currencyFormatUsd($prices->markup) }}</div>
                                                            <div class="rate-idr">{{ currencyFormatIdr($prices->markup * $usdrates->rate) }}</div>
                                                        </td>
                                                        <td>
                                                            <div class="rate-usd">{{ currencyFormatUsd($transport_tax) }}</div>
                                                            <div class="rate-idr">{{ currencyFormatIdr($transport_tax * $usdrates->rate) }}</div>
                                                        </td>
                                                        <td>
                                                            <div class="rate-usd">{{ currencyFormatUsd($transport_final_price) }}</div>
                                                            <div class="rate-idr">{{ currencyFormatIdr($transport_final_price*$usdrates->rate) }}</div>
                                                        </td>
                                                        <form id="delete-price-{{ $prices->id }}" action="/fdelete-transport-price/{{ $prices->id }}" method="post">
                                                            @csrf
                                                            @method('delete')
                                                            <input type="hidden" name="transport_id" value="{{ $transport->id }}">
                                                            <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                        </form>
                                                        <td class="text-right">
                                                            <div class="table-action">
                                                                <a href="#" data-toggle="modal" data-target="#detail-price-{{ $prices->id }}">
                                                                    <button class="btn-view" data-toggle="tooltip" data-placement="top" title="Detail"><i class="dw dw-eye"></i></button>
                                                                </a>
                                                                @canany(['posDev','posAuthor'])
                                                                    <a href="#" data-toggle="modal" data-target="#edit-price-{{ $prices->id }}">
                                                                        <button class="btn-edit" data-toggle="tooltip" data-placement="top" title="Edit"><i class="icon-copy fa fa-pencil"></i></button>
                                                                    </a>
                                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" form="delete-price-{{ $prices->id }}" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                                @endcanany
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    {{-- MODAL PRICE DETAIL --------------------------------------------------------------------------------------------------------------- --}}
                                                    <div class="modal fade" id="detail-price-{{ $prices->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <div class="title"><i class="fa fa-eye"></i> Detail Price</div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-md-6 flex-end">
                                                                            <div class="card-title">{{ $transport->name }}</div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="card-subtitle m-t-4">{{ $prices->type }}</div>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <hr class="form-hr">
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    @if ($prices->type == "Daily Rent")
                                                                                        <div class="col-12">
                                                                                            <div class="card-subtitle">Duration:</div>
                                                                                            @if ($prices->duration <= 6)
                                                                                            <p> {{ "Half Day (". $prices->duration." Hours)" }}</p>
                                                                                            @else
                                                                                                <p> {{ "Full Day (". $prices->duration." Hours)" }}</p>
                                                                                            @endif
                                                                                        </div>
                                                                                    @else
                                                                                        @if (isset($prices->src))
                                                                                            <div class="col-12">
                                                                                                <div class="card-subtitle">Origin</div>
                                                                                                <p>{{ $prices->src }}</p>
                                                                                            </div>
                                                                                        @endif
                                                                                        @if (isset($prices->dst))
                                                                                            <div class="col-12">
                                                                                                <div class="card-subtitle">Destination</div>
                                                                                                <p>{{ $prices->dst }}</p>
                                                                                            </div>
                                                                                        @endif
                                                                                        <div class="col-12">
                                                                                            <div class="card-subtitle">Duration</div>
                                                                                            <p>{{ $prices->duration." Hours" }}</p>
                                                                                        </div>
                                                                                    @endif
                                                                                    <div class="col-12">
                                                                                        <div class="card-subtitle">Extra Time:</div>
                                                                                        <p>{{ $prices->extra_time." % ($ ". number_format(((ceil($prices->contract_rate / $usdrates->rate)+$prices->markup)*$prices->extra_time)/100)."/hour)" }}</p>
                                                                                    </div>
                                                                                    @if ($prices->additional_info != "")
                                                                                        <div class="col-12">
                                                                                            <div class="card-subtitle">Additional Information:</div>
                                                                                        <p>{!! $prices->additional_info !!}</p>
                                                                                        </div>
                                                                                    @endif
                                                                                </div> 
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-6">
                                                                            <div class="card-text">
                                                                                <div class="row ">
                                                                                    <div class="col-12">
                                                                                        <div class="card-subtitle">Contract Rate:</div>
                                                                                        <div class="rate-usd">{{ currencyFormatUsd($usd_transport) }}</div>
                                                                                        <div class="rate-idr">{{ currencyFormatIdr($prices->contract_rate) }}</div>
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <div class="card-subtitle">Markup:</div>
                                                                                        <div class="rate-usd">{{ currencyFormatUsd($prices->markup) }}</div>
                                                                                        <div class="rate-idr">{{ currencyFormatIdr($prices->markup * $usdrates->rate) }}</div>
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <div class="card-subtitle">Tax:</div>
                                                                                        <div class="rate-usd">{{ currencyFormatUsd($transport_tax) }}</div>
                                                                                        <div class="rate-idr">{{ currencyFormatIdr($transport_tax * $usdrates->rate) }}</div>
                                                                                    </div>
                                                                                    <div class="col-12">
                                                                                        <div class="card-subtitle">Published Rate:</div>
                                                                                        <div class="price-usd">{{ currencyFormatUsd($transport_final_price) }}</div>
                                                                                        <div class="price-idr">{{ currencyFormatIdr($transport_final_price*$usdrates->rate) }}</div>
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
                                                    {{-- MODAL PRICE EDIT --------------------------------------------------------------------------------------------------------------- --}}
                                                    @canany(['posDev','posAuthor'])
                                                        <div class="modal fade" id="edit-price-{{ $prices->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <div class="title"><i class="fa fa-pencil"></i> Edit Price {{ $transport->name }}</div>
                                                                        </div>
                                                                    
                                                                        <form id="update-price-{{ $prices->id }}" action="/fupdate-transport-price/{{ $prices->id }}" method="post" enctype="multipart/form-data">
                                                                            @method('put')
                                                                            {{ csrf_field() }}
                                                                            <div class="row">
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group row">
                                                                                        <label for="type" class="col-12 col-sm-12 col-md-12 col-form-label">Type </label>
                                                                                        <div class="col-12 col-sm-12 col-md-12">
                                                                                            <select id="type" name="type" class="custom-select col-12 @error('type') is-invalid @enderror" required>
                                                                                                <option selected value="{{ $prices->type }}">{{ $prices->type }}</option>
                                                                                                <option value="Daily Rent">Daily Rent</option>
                                                                                                <option value="Airport Shuttle">Airport Shuttle</option>
                                                                                                <option value="Transfers">Transfers</option>
                                                                                            </select>
                                                                                            @error('type')
                                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group row">
                                                                                        <label for="duration" class="col-12 col-sm-12 col-md-12 col-form-label">Duration </label>
                                                                                        <div class="col-md-12">
                                                                                            <input type="number" id="duration" name="duration" class="input-icon form-control @error('duration') is-invalid @enderror" placeholder="Insert duration" value="{{ $prices->duration }}" required>
                                                                                            @error('duration')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group row">
                                                                                        <label for="src" class="col-12 col-sm-12 col-md-12 col-form-label">Origin</label>
                                                                                        <div class="col-md-12">
                                                                                            <input type="text" id="src" name="src" class="input-icon form-control @error('src') is-invalid @enderror" placeholder="Insert origin" value="{{ $prices->src }}">
                                                                                            @error('src')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group row">
                                                                                        <label for="dst" class="col-12 col-sm-12 col-md-12 col-form-label">Destination</label>
                                                                                        <div class="col-md-12">
                                                                                            <input type="text" id="dst" name="dst" class="input-icon form-control @error('dst') is-invalid @enderror" placeholder="Insert destination" value="{{ $prices->dst }}">
                                                                                            @error('dst')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group row">
                                                                                        <label for="extra_time" class="col-12 col-sm-12 col-md-12 col-form-label">Extra Time</label>
                                                                                        <div class="col-md-12">
                                                                                            <div class="btn-icon">
                                                                                                <span>%</span>
                                                                                                <input type="number" id="extra_time" name="extra_time" class="input-icon form-control @error('extra_time') is-invalid @enderror" placeholder="Insert extra time" value="{{ $prices->extra_time }}" required>
                                                                                                @error('extra_time')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group row">
                                                                                        <label for="contract_rate" class="col-12 col-sm-12 col-md-12 col-form-label">Contract Rate </label>
                                                                                        <div class="col-md-12">
                                                                                            <div class="btn-icon">
                                                                                                <span>Rp</span>
                                                                                                <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ $prices->contract_rate }}" required>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-6">
                                                                                    <div class="form-group row">
                                                                                        <label for="markup" class="col-12 col-sm-12 col-md-12 col-form-label">Markup </label>
                                                                                        <div class="col-md-12">
                                                                                            <div class="btn-icon">
                                                                                                <span>$</span>
                                                                                                <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert Markup" value="{{ $prices->markup }}">
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-md-12">
                                                                                    <div class="form-group row">
                                                                                        <label for="additional_info" class="col-sm-12 col-md-12 col-form-label">Additional Information</label>
                                                                                        <div class="col-sm-12 col-md-12">
                                                                                            <textarea name="additional_info" id="additional_info" wire:model="additional_info" class="textarea_editor form-control @error('additional_info') is-invalid @enderror" placeholder="Select Date and Time" type="text">{!! $prices->additional_info !!}</textarea>
                                                                                            @error('additional_info')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="col-12 text-right m-t-8">
                                                                                    <input id="transports_id" name="transports_id" value="{{ $transport->id }}" type="hidden">
                                                                                    <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                                    <input id="service_id" name="service_id" value="{{ $prices->id }}" type="hidden">
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                        <div class="card-box-footer">
                                                                            <button type="submit" form="update-price-{{ $prices->id }}" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Update</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endcanany
                                               @endforeach
                                            </tbody>
                                        </table>
                                        @canany(['posDev','posAuthor'])
                                            <div class="card-box-footer">
                                                <a href="#" data-toggle="modal" data-target="#add-transport-price-{{ $transport->id }}"><button class="btn btn-primary"><i class="ion-plus-round"></i> Add Price</button></a>
                                            </div>
                                            {{-- MODAL ADD TRANSPORT PRICE  --------------------------------------------------------------------------------------------------------------- --}}
                                            <div class="modal fade" id="add-transport-price-{{ $transport->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered" role="document">
                                                    <div class="modal-content">
                                                        <div class="card-box">
                                                            <div class="card-box-title">
                                                                <div class="title"><i class="fa fa-plus"></i> Add Price {{ $transport->name }}</div>
                                                            </div>
                                                            
                                                            <form id="createPrices" action="/fadd-transport-price" method="post" enctype="multipart/form-data">
                                                                @csrf
                                                                {{ csrf_field() }}
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="type">Type <span> *</span></label>
                                                                            <select id="type" name="type" class="custom-select col-12 @error('type') is-invalid @enderror" required>
                                                                                <option selected value="">Select type</option>
                                                                                <option value="Daily Rent">Daily Rent</option>
                                                                                <option value="Airport Shuttle">Airport Shuttle</option>
                                                                                <option value="Transfers">Transfers</option>
                                                                            </select>
                                                                            @error('type')
                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="duration">Duration</label>
                                                                            <input type="number" id="duration" name="duration" class="input-icon form-control @error('duration') is-invalid @enderror" placeholder="Insert duration" value="{{ old('duration') }}" required>
                                                                            @error('duration')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="src">Origin</label>
                                                                            <input type="text" id="src" name="src" class="input-icon form-control @error('src') is-invalid @enderror" placeholder="Optional" value="{{ old('src') }}" >
                                                                            @error('src')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="dst">Destination</label>
                                                                            <input type="text" id="dst" name="dst" class="input-icon form-control @error('dst') is-invalid @enderror" placeholder="Optional" value="{{ old('dst') }}" >
                                                                            @error('dst')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="contract_rate">Contract Rate <span> *</span></label>
                                                                            <div class="btn-icon">
                                                                                <span>Rp</span>
                                                                                <input type="number" id="contract_rate" name="contract_rate" class="input-icon form-control @error('contract_rate') is-invalid @enderror" placeholder="Insert contract rate" value="{{ old('contract_rate') }}" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="markup">Markup <span> *</span></label>
                                                                            <div class="btn-icon">
                                                                                <span>$</span>
                                                                                <input type="number" id="markup" name="markup" class="input-icon form-control @error('markup') is-invalid @enderror" placeholder="Insert Markup" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="extra_time">Extra Time <span> *</span></label>
                                                                            <div class="btn-icon">
                                                                                <span>%</span>
                                                                                <input type="number" id="extra_time" name="extra_time" class="input-icon form-control @error('extra_time') is-invalid @enderror" placeholder="Insert extra time" value="{{ old('extra_time') }}" required>
                                                                                @error('extra_time')
                                                                                    <span class="invalid-feedback">
                                                                                        <strong>{{ $message }}</strong>
                                                                                    </span>
                                                                                @enderror
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-12">
                                                                        <div class="form-group">
                                                                            <label for="additional_info">Additional Information</label>
                                                                            <textarea name="additional_info" id="additional_info" wire:model="additional_info" class="textarea_editor form-control @error('additional_info') is-invalid @enderror" placeholder="Optional" type="text">{!! old('additional_info') !!}</textarea>
                                                                            @error('additional_info')
                                                                                <span class="invalid-feedback">
                                                                                    <strong>{{ $message }}</strong>
                                                                                </span>
                                                                            @enderror
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <input name="transports_id" value="{{ $transport->id }}" type="hidden">
                                                                <input name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                            </form>
                                                            <div class="card-box-footer">
                                                                <button type="submit" form="createPrices" class="btn btn-primary"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add</button>
                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endcanany
                                    </div>
                                </div>
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
    function searchPriceByType() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchPriceByType");
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
    function searchPriceByDuration() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchPriceByDuration");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbPrice");
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