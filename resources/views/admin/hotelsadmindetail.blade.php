@php
    use Carbon\Carbon;
@endphp
@extends('layouts.head')
@section('title', __('messages.Hotel Detail'))
@section('content')
    <div class="mobile-menu-overlay"></div>
    @can('isAdmin')
        <div class="main-container">
            <div class="pd-ltr-20">
                <div class="min-height-200px">
                    <div class="page-header">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="title"><i class="micon dw dw-hotel" aria-hidden="true"></i> Hotel</div>
                                <nav aria-label="breadcrumb" role="navigation">
                                    <ol class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="/admin-panel">Admin Panel</a></li>
                                        <li class="breadcrumb-item"><a href="/hotels-admin">Hotels</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">{{ $hotel->name }}</li>
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
                        @if (session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                        {{-- ATTENTIONS MOBILE --}}
                        <div class="col-md-4 mobile">
                            <div class="row">
                                @include('admin.usd-rate')
                                @include('layouts.attentions')
                                <div class="col-md-12 m-b-18">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <div class="subtitle"><i class="icon-copy ion-ios-pulse-strong"></i> Log</div>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <p><b>{{ $hotel->name }}</b></p>
                                            </div>
                                            <div class="col-6">
                                                <p><b>{{ dateTimeFormat($hotel->created_at) }}</b></p>
                                            </div>
                                            <div class="col-12">
                                                <hr class="form-hr">
                                            </div>
                                            <div class="col-6">
                                                <p><b>Author :</b> {{ $author->name }}</p>
                                            </div>
                                            <div class="col-6 text-right">
                                                <p><i>{{ Carbon::parse($hotel->created_at)->diffForHumans();  }}</i></p>
                                            </div>
                                            <div class="col-md-12">
                                                <p><b>Rooms :</b> {{ count($hotel->rooms)." Type" }}</p>
                                                <p><b>Last Price :</b> {{ dateFormat($latest_price->date) }}</p>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            {{-- HOTEL DETAIL --}}
                            <div class="card-box m-b-18">
                                <div class="card-box-title">
                                    <div class="subtitle">
                                        <i class="fa fa-file-text" aria-hidden="true"></i> Hotel Detail
                                    </div>
                                    <div class="status-card m-t-8">
                                        @include('partials.status-icon', ['status' => $hotel->status])
                                    </div>
                                </div>
                                <div class="card-box-body">
                                    <div class="modal-image m-b-18">
                                        <img src="{{ asset ('storage/hotels/hotels-cover/' . $hotel->cover) }}" alt="{{ $hotel->name }}" loading="lazy">
                                    </div>
                                    <div class="card-content">
                                        <div class="image-caption-title">
                                            {{ $hotel->name }}<br>
                                            <a href="{{ $hotel->web }}">
                                               {{ $hotel->web }}
                                            </a>
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="card-subtitle">Contact Person</div>
                                                <p>{{ $hotel->contact_person }}</p>
                                            </div>
                                            <div class="col-6">
                                                <div class="card-subtitle">Phone</div>
                                                <p>{{ $hotel->phone }}</p>
                                            </div>
                                            
                                        </div>
                                        <div class="row">
                                            <div class="col-6">
                                                <div class="card-subtitle">Address</div>
                                                <p>{{ $hotel->address }}</p>
                                            </div>
                                            <div class="col-6">
                                                <div class="card-subtitle">Region</div>
                                                <a target="__blank" href="{{ $hotel->map }}">
                                                    <p class="text"><i class="icon-copy fa fa-map-marker" aria-hidden="true"></i>{{ " ". $hotel->region }}</p>
                                                </a>
                                            </div>
                                            
                                            <div class="col-6">
                                                <div class="card-subtitle">Min Stay</div>
                                                <p>{{ $hotel->min_stay." nights" }}</p>
                                            </div>
                                            <div class="col-6">
                                                <div class="card-subtitle">Max Stay</div>
                                                <p>{{ $hotel->max_stay." nights" }}</p>
                                            </div>
                                        </div>
                                        @if (isset($hotel->check_in_time) and isset($hotel->check_out_time))
                                            
                                            <div class="row">
                                                @if (isset($hotel->check_out_time))
                                                    <div class="col-6">
                                                        <div class="card-subtitle">Check-out</div>
                                                        <p><i class="fa fa-clock-o" aria-hidden="true"></i> {{ date('H.i',strtotime($hotel->check_out_time)) }}</p>
                                                    </div>
                                                @endif
                                                @if (isset($hotel->check_in_time))
                                                    <div class="col-6">
                                                        <div class="card-subtitle">Check-in</div>
                                                        <p><i class="fa fa-clock-o" aria-hidden="true"></i> {{ date('H.i',strtotime($hotel->check_in_time)) }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        @if (isset($hotel->airport_distance) and isset($hotel->airport_duration))
                                            
                                            <div class="row">
                                                @if (isset($hotel->airport_distance))
                                                    <div class="col-6">
                                                        <div class="card-subtitle">Airport Distance</div>
                                                        <p><i class="fa fa-map" aria-hidden="true"></i> {{ $hotel->airport_distance." Km" }}</p>
                                                    </div>
                                                @endif
                                                @if (isset($hotel->airport_duration))
                                                    <div class="col-6">
                                                        <div class="card-subtitle">Airport Duration</div>
                                                        <p><i class="fa fa-clock-o" aria-hidden="true"></i> {{ $hotel->airport_duration." Hours" }}</p>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                        <div class="card-subtitle">Description</div>
                                        @if (app()->getLocale() == 'zh')
                                            <p>{!! $hotel->description_simplified !!}</p>
                                        @elseif (app()->getLocale() == 'zh-CN')
                                            <p>{!! $hotel->description_traditional !!}</p>
                                        @else
                                            <p>{!! $hotel->description !!}</p>
                                        @endif
                                        @if ($hotel->facility != "")
                                            <div class="card-subtitle">Facility</div>
                                            @if (app()->getLocale() == 'zh')
                                                <p>{!! $hotel->facility_simplified !!}</p>
                                            @elseif (app()->getLocale() == 'zh-CN')
                                                <p>{!! $hotel->facility_traditional !!}</p>
                                            @else
                                                <p>{!! $hotel->facility !!}</p>
                                            @endif
                                        @endif
                                        @if ($hotel->benefits != "")
                                            <div class="card-subtitle">Benefits</div>
                                            @if (app()->getLocale() == 'zh')
                                                <p>{!! $hotel->benefits_simplified !!}</p>
                                            @elseif (app()->getLocale() == 'zh-CN')
                                                <p>{!! $hotel->benefits_traditional !!}</p>
                                            @else
                                                <p>{!! $hotel->benefits !!}</p>
                                            @endif
                                        @endif
                                        @if ($hotel->optional_rate != "")
                                            <div class="card-subtitle">Additional Charge</div>
                                            <p>{!! $hotel->optional_rate !!}</p>
                                        @endif
                                        @if ($hotel->additional_info != "")
                                            <div class="card-subtitle">Additional Information</div>
                                            @if (app()->getLocale() == 'zh')
                                                <p>{!! $hotel->additional_info_simplified !!}</p>
                                            @elseif (app()->getLocale() == 'zh-CN')
                                                <p>{!! $hotel->additional_info_traditional !!}</p>
                                            @else
                                                <p>{!! $hotel->additional_info !!}</p>
                                            @endif
                                        @endif
                                        @if ($hotel->cancellation_policy != "")
                                            <div class="card-subtitle">Cancellation Policy</div>
                                            @if (app()->getLocale() == 'zh')
                                                <p>{!! $hotel->cancellation_policy_simplified !!}</p>
                                            @elseif (app()->getLocale() == 'zh-CN')
                                                <p>{!! $hotel->cancellation_policy_traditional !!}</p>
                                            @else
                                                <p>{!! $hotel->cancellation_policy !!}</p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                <div class="card-box-footer">
                                    @canany(['posDev','posAuthor'])
                                        <a href="edit-hotel-{{ $hotel->id }}">
                                            <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Hotel</button>
                                        </a>
                                    @endcanany
                                </div>
                            </div>
                            {{-- CONTRACT --}}
                            <div id="contracts" class="card-box m-b-18">
                                <div class="card-box-title">
                                    <i class="fa fa-pencil-square-o" aria-hidden="true"></i> Contrac
                                </div>
                                <div class="card-box-body">
                                    @if (count($contracts) > 0)
                                        <div class="card-box-content">
                                            @foreach ($contracts->where('hotels_id',$hotel->id) as $contract)
                                                <div class="card-contract p-8">
                                                    <div class="card-subtitle"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> {{ $contract->name }}</div>
                                                    <p><i>{{ date('d M y',strtotime($contract->period_start)).' - '.date('d M y',strtotime($contract->period_end)) }}</i></p>
                                                    <hr class="form-hr">
                                                    <div class="card-action text-right">
                                                        <a class="action-btn" href="#" data-toggle="modal" data-target="#contract-{{ $contract->id }}">
                                                            <i class="icon-copy fa fa-eye" aria-hidden="true"></i>
                                                        </a>
                                                        @canany(['posDev','posAuthor'])
                                                            <a class="action-btn" href="#" data-toggle="modal" data-target="#edit-contract-{{ $contract->id }}">
                                                                <i class="icon-copy fa fa-pencil" aria-hidden="true"></i>
                                                            </a>
                                                            <a class="action-btn" href="#" data-toggle="modal" data-target="#delete-contract-{{ $contract->id }}">
                                                                <i class="icon-copy fa fa-trash" aria-hidden="true"></i>
                                                            </a>
                                                        @endcanany
                                                    </div>
                                                    {{-- MODAL VIEW CONTRACT --}}
                                                    <div class="modal fade" id="contract-{{ $contract->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content" style="padding: 0; background-color:transparent; border:none;">
                                                                <div class="modal-body pd-5">
                                                                    <embed src="storage/hotels/hotels-contract/{{ $contract->file_name }}" frameborder="10" width="100%" height="850px">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @canany(['posDev','posAuthor'])
                                                        {{-- MODAL DELETE CONTRACT --}}
                                                        <div class="modal fade" id="delete-contract-{{ $contract->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Delete Contract
                                                                        </div>
                                                                        <div class="card-box-body">
                                                                            <div class="row">
                                                                                <div class="col-md-3">
                                                                                    <p><b>File Name:</b></p>
                                                                                    <p>{{ $contract->file_name }}</p>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <p><b>Contract Name:</b></p>
                                                                                    <p>{{ $contract->name }}</p>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <p><b>Period Start:</b></p>
                                                                                    <p>{{ date('d M Y', strtotime($contract->period_start)) }}</p>
                                                                                </div>
                                                                                <div class="col-md-3">
                                                                                    <p><b>Period End:</b></p>
                                                                                    <p>{{ date('d M Y', strtotime($contract->period_end)) }}</p>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <form action="/fdelete-contract/{{ $contract->id }}" method="post">
                                                                                @csrf
                                                                                @method('delete')
                                                                                <input type="hidden" name="file_name" value="{{ $contract->file_name }}">
                                                                                <input type="hidden" name="author" value="{{ Auth::user()->id }}">
                                                                                <input type="hidden" name="hotels_id" value="{{ $hotel->id }}">
                                                                                <button class="btn btn-danger" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i> Delete</button>
                                                                                <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                                            </form>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        {{-- MODAL EDIT CONTRACT --}}
                                                        <div class="modal fade" id="edit-contract-{{ $contract->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                                <div class="modal-content">
                                                                    <div class="card-box">
                                                                        <div class="card-box-title">
                                                                            <i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit Contract
                                                                        </div>
                                                                        <div class="card-box-body">
                                                                            <form id="update-hotel-contract-{{ $contract->id }}" action="/fupdate-hotel-contract/{{ $contract->id }}" method="post" enctype="multipart/form-data">
                                                                                @csrf
                                                                                @method('put')
                                                                                <div class="row">
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label for="file_name"><i class="icon-copy fa fa-file-pdf-o" aria-hidden="true"></i> {{ $contract->file_name }}</label>
                                                                                            <input type="file" name="file_name" id="file_name" class="custom-file-input @error('file_name') is-invalid @enderror" placeholder="Choose Contract">
                                                                                            @error('file_name')
                                                                                                <div class="alert alert-danger">{{ $message }}</div>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                    
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label for="contract_name">Contract Name</label>
                                                                                            <input name="contract_name" id="contract_name"  type="text" class="form-control @error('contract_name') is-invalid @enderror" placeholder="Insert contract name" value="{{ $contract->name }}">
                                                                                            @error('contract_name')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label for="period_start">Period Start</label>
                                                                                            <input readonly name="period_start" id="period_start"  type="text" class="form-control date-picker @error('period_start') is-invalid @enderror" placeholder="Insert contract name" value="{{ date('d M Y', strtotime($contract->period_start)) }}">
                                                                                            @error('period_start')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                    <div class="col-md-6">
                                                                                        <div class="form-group">
                                                                                            <label for="period_end">Period End</label>
                                                                                            <input readonly name="period_end" id="period_end"  type="text" class="form-control date-picker @error('period_end') is-invalid @enderror" placeholder="Select date" value="{{ date('d M Y', strtotime($contract->period_end)) }}">
                                                                                            @error('period_end')
                                                                                                <span class="invalid-feedback">
                                                                                                    <strong>{{ $message }}</strong>
                                                                                                </span>
                                                                                            @enderror
                                                                                        </div>
                                                                                    </div>
                                                                                    <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                                                                    <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                        <div class="card-box-footer">
                                                                            <button type="submit" form="update-hotel-contract-{{ $contract->id }}" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Update</button>
                                                                            <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endcanany
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="notif">Contract not available</div>
                                    @endif
                                </div>
                                @canany(['posDev','posAuthor'])
                                    <div class="card-box-footer">
                                        <a href="#" data-toggle="modal" data-target="#add-contract-{{ $hotel->id }}" data-toggle="tooltip" data-placement="top" title="Add more contract">
                                            <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Contract</button>
                                        </a>
                                    </div>
                                    {{-- MODAL ADD Contract =====================================================================================================================--}}
                                    <div class="modal fade" id="add-contract-{{ $hotel->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="card-box">
                                                    <div class="card-box-title">
                                                        <i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Contract
                                                    </div>
                                                    <div class="card-box-body">
                                                        <form id="addContract" action="/fadd-hotel-contract" method="post" enctype="multipart/form-data">
                                                            @csrf
                                                            {{ csrf_field() }}
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="file_name">Contract PDF File</label>
                                                                        <input type="file" name="file_name" id="file_name" class="custom-file-input @error('file_name') is-invalid @enderror" placeholder="Choose Contract" value="{{ old('file_name') }}" required>
                                                                        @error('file_name')
                                                                            <div class="alert alert-danger">{{ $message }}</div>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="contract_name">Contract Name</label>
                                                                        <input name="contract_name" id="contract_name"  type="text" class="form-control @error('contract_name') is-invalid @enderror" placeholder="Insert contract name" value="{{ old('contract_name') }}" required>
                                                                        @error('contract_name')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="period_start" >Period Start</label>
                                                                        <input readonly name="period_start" id="period_start"  type="text" class="form-control date-picker @error('period_start') is-invalid @enderror" placeholder="Select Date" value="{{ old('period_start') }}" required>
                                                                        @error('period_start')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="period_end">Period End</label>
                                                                        <input name="period_end" id="period_end"  type="text" class="form-control date-picker @error('period_end') is-invalid @enderror" placeholder="Select date" value="{{ old('period_end') }}" required>
                                                                        @error('period_end')
                                                                            <span class="invalid-feedback">
                                                                                <strong>{{ $message }}</strong>
                                                                            </span>
                                                                        @enderror
                                                                    </div>
                                                                </div>
                                                                
                                                                <input name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                                                <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="card-box-footer">
                                                        <button type="submit" form="addContract" class="btn btn-primary"><i class="icon-copy fa fa-check" aria-hidden="true"></i> Add</button>
                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endcanany
                            </div>
                        </div>
                        {{-- ATTENTIONS DESKTOP --}}
                        <div class="col-md-4 desktop">
                            <div class="row">
                                @include('admin.usd-rate')
                                @include('layouts.attentions')
                                <div class="col-md-12">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <i class="icon-copy ion-ios-pulse-strong"></i> Log
                                        </div>
                                        <div class="card-box-body">
                                            <div class="row">
                                                <div class="col-6">
                                                    <p><b>{{ $hotel->name }}</b></p>
                                                </div>
                                                <div class="col-6">
                                                    <p><b>{{ dateTimeFormat($hotel->created_at) }}</b></p>
                                                </div>
                                                <div class="col-12">
                                                    <hr class="form-hr">
                                                </div>
                                                <div class="col-6">
                                                    <p><b>Author :</b> {{ $author->name }}</p>
                                                </div>
                                                <div class="col-6 text-right">
                                                    <p><i>{{ Carbon::parse($hotel->created_at)->diffForHumans();  }}</i></p>
                                                </div>
                                                <div class="col-md-12">
                                                    <p><b>Rooms :</b> {{ count($hotel->rooms)." Type" }}</p>
                                                    <p><b>Last Price :</b> {{ dateFormat($latest_price->date) }}</p>
                                                </div>
                                                
                                            </div>
                                        </div>
                                        <div class="card-box-footer"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- ROOM --}}
                    <div id="rooms" class="product-wrap m-b-18">
                        <div class="product-detail-wrap">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="card-box">
                                        <div class="card-box-title">
                                            <i class="fa fa-bed" aria-hidden="true"></i> Suites & Villa
                                        </div>
                                        <div class="card-box-body">
                                        @if (count($hotel->rooms) > 0)
                                            <div class="card-box-content">
                                                @foreach ($hotel->rooms as $room)
                                                    <div class="card">
                                                        <a href="#" data-toggle="modal" data-target="#detail-room-{{ $room->id }}">
                                                            <div class="card-image-container">
                                                                <div class="card-status">
                                                                    @include('partials.status-icon', ['status' => $room->status])
                                                                </div>
                                                                <img class="img-fluid rounded thumbnail-image {{ $room->status == "Draft"?"grayscale":"" }}" src="{{ url('storage/hotels/hotels-room/' . $room->cover) }}" alt="{{ $room->rooms }}">
                                                                <div class="name-card">
                                                                    <p>
                                                                        {{ $room->rooms }}
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </a>
                                                        @canany(['posDev','posAuthor'])
                                                            <div class="card-btn-container">
                                                                <a href="/edit-room-{{ $room->id }}">
                                                                    <button class="btn-update" data-toggle="tooltip" data-placement="top" title="Update"><i class="icon-copy fa fa-pencil"></i></button><br>
                                                                    {{-- <button type="button" class="btn btn-update"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i></button> --}}
                                                                </a>
                                                                <form action="/delete-room/{{ $room->id }}" method="post">
                                                                    @csrf
                                                                    @method('delete')
                                                                    <input id="author" name="author" value="{{ Auth::user()->id }}" type="hidden">
                                                                    <input id="hotels_id" name="hotels_id" value="{{ $hotel->id }}" type="hidden">
                                                                    <button class="btn-delete" onclick="return confirm('Are you sure?');" type="submit" data-toggle="tooltip" data-placement="top" title="Delete"><i class="icon-copy fa fa-trash"></i></button>
                                                                </form>
                                                            </div>
                                                        @endcanany
                                                    </div>
                                                    {{-- MODAL ROOM DETAIL --}}
                                                    <div class="modal fade" id="detail-room-{{ $room->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel" aria-hidden="true">
                                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                                            <div class="modal-content">
                                                                <div class="card-box">
                                                                    <div class="card-box-title">
                                                                        <i class="fa fa-bed" aria-hidden="true"></i> {{ $room->rooms }}
                                                                        <div class="status-card m-t-8">
                                                                            @include('partials.status-icon', ['status' => $room->status])
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-box-body">
                                                                        <div class="row">
                                                                            <div class="col-md-12">
                                                                                <div class="page-card">
                                                                                    <div class="modal-image">
                                                                                        <img src="{{ asset ('storage/hotels/hotels-room/' . $room->cover) }}" alt="{{ $room->rooms }}" loading="lazy">
                                                                                    </div>
                                                                                    <div class="row ">
                                                                                        <div class="col-sm-6">
                                                                                            <div class="card-subtitle">Capacity</div>
                                                                                            <p>
                                                                                                {{ $room->capacity_adult." Adult" }}
                                                                                                @if ($room->capacity_child > 0)
                                                                                                     + {{ $room->capacity_child." Child" }}
                                                                                                @endif
                                                                                            </p>
                                                                                        </div>
                                                                                        @if ($room->view != "")
                                                                                            <div class="col-sm-6">
                                                                                                <div class="card-subtitle">View</div>
                                                                                                <p>{{ $room->view }}</p>
                                                                                            </div>
                                                                                        @endif
                                                                                        @if ($room->beds != "")
                                                                                            <div class="col-sm-6">
                                                                                                <div class="card-subtitle">Bed</div>
                                                                                                <p>{{ $room->beds }}</p>
                                                                                            </div>
                                                                                        @endif
                                                                                        @if ($room->size != "")
                                                                                            <div class="col-sm-6">
                                                                                                <div class="card-subtitle">Room Size (m²)</div>
                                                                                                <p>{!! $room->size !!} m²</p>
                                                                                            </div>
                                                                                        @endif
                                                                                        @if ($room->amenities != "")
                                                                                            <div class="col-sm-12">
                                                                                                <div class="card-subtitle">Amenities</div>
                                                                                                <p>
                                                                                                    @if (app()->getLocale() == 'zh')
                                                                                                        {!! $room->amenities_traditional !!}
                                                                                                    @elseif (app()->getLocale() == 'zh-CN')
                                                                                                        {!! $room->amenities_simplified !!}
                                                                                                    @else
                                                                                                        {!! $room->amenities !!}
                                                                                                    @endif
                                                                                                </p>
                                                                                            </div>
                                                                                        @endif
                                                                                        @if ($room->additional_info != "")
                                                                                            <div class="col-sm-12">
                                                                                                <div class="card-subtitle">Additional Information</div>
                                                                                                <p>
                                                                                                    @if (app()->getLocale() == 'zh')
                                                                                                        {!! $room->additional_info_traditional !!}
                                                                                                    @elseif (app()->getLocale() == 'zh-CN')
                                                                                                        {!! $room->additional_info_simplified !!}
                                                                                                    @else
                                                                                                        {!! $room->additional_info !!}
                                                                                                    @endif
                                                                                                </p>
                                                                                            </div>
                                                                                        @endif
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="card-box-footer">
                                                                        @canany(['posDev','posAuthor'])
                                                                            <a href="/edit-room-{{ $room->id }}">
                                                                                <button type="button" class="btn btn-primary"><i class="icon-copy fa fa-pencil" aria-hidden="true"></i> Edit</button>
                                                                            </a>
                                                                        @endcanany
                                                                        <button type="button" class="btn btn-danger" data-dismiss="modal"><i class="icon-copy fa fa-close" aria-hidden="true"></i> Cancel</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <div class="notif">!!! The hotel doesn't have a rooms yet, please add a rooms now!</div>
                                        @endif
                                        </div>
                                        <div class="card-box-footer">
                                            @canany(['posDev','posAuthor'])
                                                <a href="add-room-{{ $hotel->id }}">
                                                    <button type="button" class="btn btn-primary btn-sm"><i class="icon-copy fa fa-plus" aria-hidden="true"></i> Add Rooms</button>
                                                </a>
                                            @endcanany
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if (count($hotel->rooms) > 0)
                        {{-- EXTRA BED ------------------------------------------------------------------------------------------------------- --}}
                        @include('admin.extrabed')
                        {{-- OPTIONAL RATE --------------------------------------------------------------------------------------------------- --}} 
                        @include('admin.additional-charge')
                        {{-- PRICE ----------------------------------------------------------------------------------------------------------- --}}
                        @include('admin.hotel-normal-price')
                        {{-- PROMO ----------------------------------------------------------------------------------------------------------- --}} 
                        @include('admin.hotel-promo-price')
                        {{-- PACKAGE --------------------------------------------------------------------------------------------------------- --}} 
                        @include('admin.hotel-package-price')
                    @endif
                </div>
                @include('layouts.footer')
            </div>
        </div>
    @endcan
@endsection
<script>
    function searchPriceByRoom() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchPriceByRoom");
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
<script>
    function searchPromoByName() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchPromoByName");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbPromo");
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
<script>
    function searchPackageByName() {
        var input, filter, table, tr, td, i, txtValue;
        input = document.getElementById("searchPackageByName");
        filter = input.value.toUpperCase();
        table = document.getElementById("tbPackage");
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
